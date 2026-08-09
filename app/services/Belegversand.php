<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\Admin\AdminBelege;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Admin\AdminRechnungen;
use Sartu\Data\AuditProtokoll;
use Sartu\Data\Zahlungseingaenge;
use Sartu\Helpers\Format;

/**
 * Einen abgelegten Beleg per Mail verschicken — `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 9.
 *
 * §9: „Der Versand ist eine **Handlung des Betreibers**, kein Automatismus. Ein Knopf je
 * Beleg, der die Mail mit dem Beleg im Anhang verschickt und den Versand protokolliert. Ein
 * zweiter Versand ist erlaubt und wird ebenfalls protokolliert."
 *
 * ## Drei Dinge, die hier nicht passieren
 *
 * **Es wird nichts neu erzeugt.** Verschickt wird die Datei, die in der Ablage liegt — §7:
 * „Ein Beleg wird **nie** neu erzeugt, um ihn wieder lesbar zu machen."
 *
 * **Es wird nichts ungeprueft verschickt.** Der Inhalt kommt ueber `Belegerzeugung::inhalt()`
 * und damit durch den Pruefsummenvergleich. Weicht die Datei ab, wirft der Abruf — und diese
 * Klasse faengt das als Fehlermeldung, statt eine veraenderte Datei hinauszuschicken.
 *
 * **Es wird kein Zustand veraendert.** Eine Mail ist kein Zahlungsvorgang.
 */
final class Belegversand
{
    public function __construct(
        private readonly AdminNachweis $nachweis,
        private readonly ?Versender $mail = null,
        private readonly ?Belegerzeugung $belege = null,
        private readonly ?AuditProtokoll $audit = null,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * @return list<string> leer bei Erfolg
     */
    public function senden(string $belegId, ?string $ip): array
    {
        $beleg = (new AdminBelege($this->nachweis, $this->pdo))->finden($belegId);

        if ($beleg === null) {
            return ['Diesen Beleg gibt es nicht.'];
        }

        $rechnungId = $beleg['invoice_id'] ?? null;

        if (!is_string($rechnungId)) {
            return ['Zu diesem Beleg gibt es keine Rechnung.'];
        }

        $rechnung = (new AdminRechnungen($this->nachweis, $this->pdo))->finden($rechnungId);
        $projekt = (new Zahlungseingaenge($this->pdo))->projektZurRechnung($rechnungId);

        if ($rechnung === null || $projekt === null) {
            return ['Zu diesem Beleg gibt es keine Rechnung.'];
        }

        try {
            $inhalt = $this->belege()->inhalt($beleg);
        } catch (\RuntimeException $fehler) {
            // §7: Eine Abweichung ist ein Fehler und keine Warnung. Der Betreiber sieht ihn
            // im Klartext — er ist der Einzige, der etwas dagegen tun kann.
            return [$fehler->getMessage()];
        }

        $art = (string) $beleg['kind'] === 'storno' ? 'Stornorechnung' : 'Rechnung';
        $nummer = (string) $beleg['number'];

        $versendet = (new Projektmail($this->mail, $this->pdo))->anKunden(
            $projekt,
            $art . ' ' . $nummer,
            'im Anhang finden Sie die ' . $art . ' ' . $nummer . ' über '
            . Format::euro(abs((int) $rechnung['gross_cents'])) . ".\n",
            ['name' => $art . '-' . $nummer . '.pdf', 'inhalt' => $inhalt, 'typ' => 'application/pdf'],
        );

        if (!$versendet) {
            return ['Die Mail liess sich nicht versenden. Der Beleg liegt weiterhin im Bereich des Kunden.'];
        }

        // Ein zweiter Versand ist erlaubt — und erzeugt deshalb einen **zweiten** Eintrag.
        // `documents` bekommt keinen Zähler: Ein Feld „wie oft versendet" wäre eine Zahl
        // ohne Zeitpunkt und ohne Empfänger, und das Protokoll hat beides.
        $this->audit()->schreiben(
            aktion: 'beleg_versendet',
            objektart: 'document',
            objektId: $belegId,
            akteurBenutzerId: $this->nachweis->adminBenutzerId,
            organisationId: (string) $projekt['organization_id'],
            grund: $art . ' ' . $nummer . ' per Mail versendet',
            detail: ['rechnung_id' => $rechnungId, 'pruefsumme' => (string) $beleg['checksum']],
            ip: $ip,
        );

        return [];
    }

    private function belege(): Belegerzeugung
    {
        return $this->belege ?? new Belegerzeugung(pdo: $this->pdo);
    }

    private function audit(): AuditProtokoll
    {
        return $this->audit ?? new AuditProtokoll($this->pdo);
    }
}
