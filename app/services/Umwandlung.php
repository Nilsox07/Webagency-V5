<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\Admin\AdminAnfragen;
use Sartu\Data\Admin\AdminBenutzer;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Admin\AdminOrganisationen;
use Sartu\Data\Admin\AdminProjekte;
use Sartu\Data\AuditProtokoll;
use Sartu\Helpers\Env;

/**
 * `In Kunde und Projekt umwandeln` — Portal-Lastenheft §4b.5.
 *
 * > „**Regel:** Anfrage ≠ Kunde. Ein Zugang entsteht ausschließlich durch diesen bewussten
 * > Klick — nie automatisch."
 *
 * Deshalb steht das hier und nicht im `AnfrageService`. Der Bedarfsscheck legt einen `lead`
 * an und nichts sonst (Testfall 29); erst ein Mensch entscheidet, dass daraus ein Kunde
 * wird.
 *
 * ## Was in einem Zug entsteht
 *
 * | Was | Woraus |
 * |---|---|
 * | `organizations` | Firmenname und E-Mail aus der Anfrage |
 * | `users` mit Rolle `kunde` | Name und E-Mail aus der Anfrage, **ohne** Passwort |
 * | `projects` | empfohlenes Paket, Zustand `angebot_offen` |
 * | `leads.converted_organization_id` und `status` | Vermerk an der Anfrage |
 * | Einladungs-E-Mail | §10, „Einladung (neu angelegt)" |
 *
 * ## Vier Stellen, an denen es klemmen kann — und was dann passiert
 *
 * **Das Paket ist `unklar`.** Dann gibt es keinen Umfang und damit kein Projekt. §9.3 sieht
 * für diesen Fall eine Rückfrage vor, keine Umwandlung. Der Admin wählt das Paket beim
 * Umwandeln selbst; ohne Angabe wird abgewiesen statt geraten.
 *
 * **Die Adresse ist schon vergeben.** `users.email` ist eindeutig. Statt einer
 * Datenbankmeldung kommt ein Satz, der sagt, was zu tun ist.
 *
 * **Die Anfrage ist schon umgewandelt.** Ein zweiter Klick erzeugt keine zweite Organisation.
 *
 * **Die Einladung geht nicht raus.** Der Zugang existiert trotzdem — er wird nicht
 * zurückgerollt, weil ein Mailserver hakt. Der Admin sieht den Hinweis und kann den Link
 * erneut anfordern lassen (§6.3).
 *
 * ## Warum keine Transaktion um alles
 *
 * MySQL rollt Schemaänderungen nicht zurück — hier geht es aber um Daten, also wäre eine
 * Transaktion möglich. Sie ist trotzdem nicht drum: Der Mailversand darf nicht in ihr
 * stehen (ein offener SMTP-Aufruf hält Zeilen gesperrt), und ein Teilabbruch **nach** den
 * drei Einfügungen ist unmöglich, weil danach nur noch der Vermerk an der Anfrage folgt.
 * Bricht der ab, steht ein vollständiger Kunde da und eine Anfrage ohne Häkchen — sichtbar,
 * behebbar, und niemand hat einen halben Zugang.
 */
final class Umwandlung
{
    public function __construct(
        private readonly AdminNachweis $nachweis,
        private readonly ?Versender $mail = null,
        private readonly ?AuditProtokoll $audit = null,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * @return array{fehler:?string,organisationId:?string,projektId:?string,benutzerId:?string,mailFehler:bool}
     */
    public function ausfuehren(string $anfrageId, string $paket, ?string $ip): array
    {
        $anfragen = new AdminAnfragen($this->nachweis, $this->pdo);
        $anfrage = $anfragen->finden($anfrageId);

        if ($anfrage === null) {
            return self::fehler('Diese Anfrage gibt es nicht.');
        }

        if ($anfrage['converted_organization_id'] !== null) {
            return self::fehler('Diese Anfrage ist bereits umgewandelt.');
        }

        $zeile = Preise::zeile($paket);

        if ($zeile === null) {
            return self::fehler('Bitte wählen Sie den Umfang, mit dem das Projekt starten soll.');
        }

        $email = mb_strtolower(trim((string) $anfrage['email']));
        $benutzer = new AdminBenutzer($this->nachweis, $this->pdo);

        if ($benutzer->kennteEmail($email)) {
            return self::fehler(
                'Zu dieser E-Mail-Adresse gibt es bereits einen Zugang. '
                . 'Ordnen Sie die Anfrage dem bestehenden Kunden von Hand zu.'
            );
        }

        $organisationId = (new AdminOrganisationen($this->nachweis, $this->pdo))
            ->anlegen((string) $anfrage['company'], $email);

        $benutzerId = $benutzer->kundeAnlegen(
            $organisationId,
            $email,
            (string) $anfrage['first_name'],
            (string) $anfrage['last_name'],
        );

        $projektId = (new AdminProjekte($this->nachweis, $this->pdo))->anlegen(
            $organisationId,
            'Website ' . (string) $anfrage['company'],
            $paket,
            (int) $zeile['korrekturrunden'],
            self::schutzstufe($paket),
            // §5.1a: Ein Projekt entsteht im Zustand `angebot_offen`. Es gibt keinen
            // Zustand davor — das Angebot ist der Anfang.
            Projektstatus::ANLAGE,
        );

        $anfragen->alsUmgewandeltVermerken($anfrageId, $organisationId);

        $this->audit()->schreiben(
            aktion: 'anfrage_umgewandelt',
            objektart: 'lead',
            objektId: $anfrageId,
            akteurBenutzerId: $this->nachweis->adminBenutzerId,
            organisationId: $organisationId,
            neuerWert: $paket,
            grund: 'Umwandlung in Kunde und Projekt',
            detail: ['project_id' => $projektId, 'user_id' => $benutzerId],
            ip: $ip,
        );

        return [
            'fehler'         => null,
            'organisationId' => $organisationId,
            'projektId'      => $projektId,
            'benutzerId'     => $benutzerId,
            'mailFehler'     => !$this->einladungSenden($email, (string) $anfrage['first_name']),
        ];
    }

    /**
     * §10, Zeile „Einladung (neu angelegt)".
     *
     * Der Kerntext steht dort gebunden. Ein Anmeldelink liegt **nicht** bei: Er gälte 15
     * Minuten, und eine Einladung, die abgelaufen ist, bevor der Empfänger sie liest, ist
     * keine. Der Kunde fordert ihn auf `/login` selbst an.
     */
    private function einladungSenden(string $email, string $vorname): bool
    {
        $adresse = rtrim((string) Env::get('BASE_URL', ''), '/');

        $text = 'Guten Tag ' . $vorname . ",\n\n"
            . "Ihr Kundenbereich ist bereit. Dort finden Sie Angebot, Aufgaben, Vorschau und\n"
            . "Rechnungen an einem Ort.\n\n"
            . 'Melden Sie sich hier an: ' . $adresse . "/login\n\n"
            . "Ein Passwort brauchen Sie nicht. Sie bekommen jedes Mal einen Anmeldelink per\n"
            . "E-Mail.\n\n"
            . "Freundliche Grüße\nSARTU\n";

        try {
            $this->mailversand()->senden($email, 'Ihr Zugang zu Ihrem Kundenbereich', $text);
        } catch (MailversandFehler) {
            return false;
        }

        return true;
    }

    /**
     * Die Schutzstufe folgt dem Paket — Masterkonzept: „fest zugeordnet, keine
     * Kundenauswahl".
     */
    private static function schutzstufe(string $paket): string
    {
        return match ($paket) {
            'start'    => 's',
            'wachstum' => 'm',
            default    => 'l',
        };
    }

    /** @return array{fehler:string,organisationId:null,projektId:null,benutzerId:null,mailFehler:false} */
    private static function fehler(string $meldung): array
    {
        return [
            'fehler'         => $meldung,
            'organisationId' => null,
            'projektId'      => null,
            'benutzerId'     => null,
            'mailFehler'     => false,
        ];
    }

    private function mailversand(): Mailversand
    {
        return $this->mail ?? new Mailversand();
    }

    private function audit(): AuditProtokoll
    {
        return $this->audit ?? new AuditProtokoll($this->pdo);
    }
}
