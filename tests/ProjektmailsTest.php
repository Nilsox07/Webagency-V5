<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\Uuid;
use Sartu\Services\AngebotDienst;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Mailtexte;
use Sartu\Services\Projektstatus;
use Sartu\Sitzung;

/**
 * Die sechs Mails aus §10, die bis zum 02.08.2026 fehlten.
 *
 * ## Was hier geprüft wird — und was nicht genügt
 *
 * Ein Zähler genügt nicht. Je Mail wird geprüft: der **Auslöser** wurde ausgelöst, im
 * Postfach liegt **genau eine** Nachricht, ihr **Betreff** ist der aus §10, und sie ging an
 * den **richtigen** Empfänger. Eine Mail mit dem richtigen Betreff an die falsche Adresse ist
 * keine erfüllte Zeile aus §10.
 *
 * Der Betreff wird gegen `Mailtexte` geprüft, nicht gegen eine abgetippte Zeichenkette: Ein
 * abgetippter Satz im Test prüft nur, dass zweimal dasselbe getippt wurde.
 *
 * ## Warum ein eigenes Postfach je Fall
 *
 * `Postfach` sammelt. Zwei Auslöser im selben Fall wären zwei Nachrichten, und „genau eine"
 * ließe sich nicht mehr sagen.
 */
final class ProjektmailsTest extends Datenbankfall
{
    private const KUNDE = 'kunde@example.org';

    private const BETREUER = 'eingang@example.org';

    private string $adminId;

    private string $organisationId;

    private string $kundeId;

    private string $projektId;

    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER = ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_HOST' => 'localhost'];
        $_POST = [];
        $_GET = [];

        touch($this->arbeitsverzeichnis . '/' . InstallationsSperre::DATEINAME);

        $this->betreiberdatenAnlegen();

        $this->adminId = $this->adminAnlegen();
        $this->organisationId = $this->organisationAnlegen('Mustermann Sanitär GmbH', self::KUNDE);
        $this->kundeId = $this->kundeAnlegen($this->organisationId, self::KUNDE);
        $this->projektId = $this->projektAnlegen();
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_GET = [];

        parent::tearDown();
    }

    // ---------------------------------------------------------------- 1 Angebot gesendet

    /**
     * §10 — `Ihr Angebot von SARTU liegt bereit`, an den Kunden.
     *
     * Die dringendste der sechs: Ohne sie liegt das Angebot im Kundenbereich, und niemand
     * schickt den Kunden hin.
     */
    public function testEinGesendetesAngebotSchicktGenauEineMailAnDenKunden(): void
    {
        $postfach = new Postfach();
        $this->alsAdmin($this->adminId);

        $angebotId = $this->angebotAnlegen($postfach);

        $this->assertSame([], (new AngebotDienst($this->nachweis(), mail: $postfach))
            ->senden($angebotId, '127.0.0.1'));

        $this->assertCount(1, $postfach->mails, 'Nicht genau eine Nachricht.');
        $this->assertSame(self::KUNDE, $postfach->mails[0]['an']);
        $this->assertSame(Mailtexte::ANGEBOT_GESENDET_BETREFF, $postfach->mails[0]['betreff']);

        // Das Gültigkeitsdatum steht drin — §10 bindet die geschweifte Klammer.
        $this->assertStringContainsString(
            \Sartu\Helpers\Format::datum(\Sartu\Helpers\Format::inTagen(AngebotDienst::GUELTIGKEIT_TAGE)),
            $postfach->mails[0]['text'],
        );

        // Und „Portal" steht nirgends — die Abweichung ist festgehalten, nicht vergessen.
        $this->assertStringNotContainsStringIgnoringCase('portal', $postfach->mails[0]['text']);
    }

    /** Ein zweiter Versand schickt keine zweite Mail — er ist gar kein Versand mehr. */
    public function testEinZweitesSendenSchicktKeineZweiteMail(): void
    {
        $postfach = new Postfach();
        $this->alsAdmin($this->adminId);

        $angebotId = $this->angebotAnlegen($postfach);
        $dienst = new AngebotDienst($this->nachweis(), mail: $postfach);

        $this->assertSame([], $dienst->senden($angebotId, null));
        $this->assertNotSame([], $dienst->senden($angebotId, null));
        $this->assertCount(1, $postfach->mails);
    }

    // ------------------------------------------------------- 2 Angebot angenommen (Admin)

    /**
     * §10 — `Angebot angenommen: {Organisation}`, an SARTU.
     *
     * Die Annahme verschickt **zwei** Mails: die Bestätigung an den Kunden (gab es schon) und
     * die Kurzmeldung an SARTU (fehlte). Geprüft werden beide, samt Empfänger — ohne die
     * zweite erfuhr SARTU von einer kostenpflichtigen Beauftragung nur durch Nachsehen.
     */
    public function testEineAnnahmeSchicktZweiMailsAnBeideSeiten(): void
    {
        $postfach = new Postfach();
        $this->alsAdmin($this->adminId);
        $angebotId = $this->angebotAnlegen($postfach);
        (new AngebotDienst($this->nachweis(), mail: $postfach))->senden($angebotId, null);

        $postfach = new Postfach();
        $this->alsKunde($this->organisationId, $this->kundeId);

        $this->assertSame([], (new \Sartu\Services\Angebotsannahme($this->bereich(), mail: $postfach))
            ->annehmen($angebotId, $this->annahmeeingabe(), $this->kundeId, '127.0.0.1'));

        $this->assertCount(2, $postfach->mails, 'Es sind nicht genau zwei Nachrichten.');

        $anKunden = $this->mailAn($postfach, self::KUNDE);
        $anBetreuer = $this->mailAn($postfach, self::BETREUER);

        $this->assertSame('Bestätigung Ihrer Beauftragung', $anKunden['betreff']);
        $this->assertSame(
            Mailtexte::angebotAngenommenBetreff('Mustermann Sanitär GmbH'),
            $anBetreuer['betreff'],
        );

        // Die interne Kurzmeldung trägt Feldwerte, keine Wertung.
        $this->assertStringContainsString('AN-2026-001', $anBetreuer['text']);
        $this->assertStringContainsString('Erika Mustermann', $anBetreuer['text']);
    }

    // ---------------------------------------------------------------- 3 Neue Aufgaben

    /**
     * §10 — `Es liegen Aufgaben für Sie bereit`, an den Kunden.
     *
     * Auslöser ist die verbuchte **Anzahlung**: Dort entsteht die Aufgabenliste (§8.3). Ohne
     * die Mail bekam der Kunde eine Liste, von der ihm niemand erzählte.
     */
    public function testDieVerbuchteAnzahlungKuendigtDieAufgabenAn(): void
    {
        $postfach = new Postfach();
        $this->angebotAnnehmen($postfach);

        $this->alsAdmin($this->adminId);
        $rechnungId = $this->anzahlungAnlegen($postfach);
        $dienst = new \Sartu\Services\Rechnungsdienst($this->nachweis(), mail: $postfach);

        $this->assertSame([], $dienst->senden($rechnungId, null));
        $postfach->mails = [];

        $this->assertSame([], $dienst->zahlungEintragen($rechnungId, 119000, 'Kontoauszug', null));

        // Zwei Nachrichten: der Zahlungseingang (gab es schon) und die Aufgaben (fehlte).
        $aufgabenmail = $this->mailMitBetreff($postfach, Mailtexte::AUFGABEN_BETREFF);

        $this->assertSame(self::KUNDE, $aufgabenmail['an']);
        $this->assertStringContainsString('15 bis 25 Minuten', $aufgabenmail['text']);
        $this->assertSame(
            1,
            $this->anzahlMitBetreff($postfach, Mailtexte::AUFGABEN_BETREFF),
            'Die Aufgabenmail ging nicht genau einmal raus.',
        );
    }

    // ---------------------------------------------------------------- 4 Faktenfreigabe

    /**
     * §10 — `Freigabe bestätigt — wir starten`, **an beide**.
     *
     * Der Lieferkorridor in der Kundenmail kommt aus dem angenommenen Angebot, nicht aus
     * einer Konstante — geprüft wird die Zahl, die dort steht.
     */
    public function testDieFaktenfreigabeSchicktAnBeideSeiten(): void
    {
        $postfach = new Postfach();
        $this->angebotAnnehmen($postfach);

        $this->alsAdmin($this->adminId);
        $rechnungId = $this->anzahlungAnlegen($postfach);
        $rechnungsdienst = new \Sartu\Services\Rechnungsdienst($this->nachweis(), mail: $postfach);
        $rechnungsdienst->senden($rechnungId, null);
        $rechnungsdienst->zahlungEintragen($rechnungId, 119000, 'Kontoauszug', null);

        // Alle Pflichtaufgaben vor der Freigabe erledigen — die Sperre aus §8.3.
        $this->alsKunde($this->organisationId, $this->kundeId);
        $aufgabendienst = new \Sartu\Services\Aufgabendienst($this->bereich(), mail: $postfach);

        foreach ($this->offenePflichtaufgabenOhneFreigabe() as $aufgabeId) {
            $aufgabendienst->abschliessen($aufgabeId, ['answer_text' => 'Antwort'], $this->kundeId, null);
        }

        $postfach->mails = [];

        $this->assertSame([], $aufgabendienst->abschliessen(
            $this->freigabeaufgabe(),
            ['bestaetigung' => '1', 'granted_name' => 'Erika Mustermann'],
            $this->kundeId,
            null,
        ));

        $this->assertCount(2, $postfach->mails, 'Es sind nicht genau zwei Nachrichten.');

        $anKunden = $this->mailAn($postfach, self::KUNDE);
        $anBetreuer = $this->mailAn($postfach, self::BETREUER);

        $this->assertSame(Mailtexte::FREIGABE_BETREFF, $anKunden['betreff']);
        $this->assertSame(Mailtexte::FREIGABE_BETREFF, $anBetreuer['betreff']);

        // §4c: Paket `wachstum` liefert den Korridor. Die Zahl steht im Angebot, nicht hier.
        $korridor = \Sartu\Services\Angebotstexte::lieferkorridor('wachstum');
        $this->assertStringContainsString(
            $korridor[0] . '–' . $korridor[1] . ' Werktagen',
            $anKunden['text'],
        );
    }

    // ---------------------------------------------------------------- 5 Antwort auf Nachricht

    /** §10 — `Antwort auf Ihre Nachricht`, an den Kunden. */
    public function testEineBeantworteteNachrichtErreichtDenKunden(): void
    {
        $nachrichtId = $this->nachrichtAnlegen();
        $postfach = new Postfach();
        $this->alsAdmin($this->adminId);

        $this->assertSame([], (new \Sartu\Services\Nachrichtendienst($this->nachweis(), mail: $postfach))
            ->beantworten($nachrichtId, 'Ihre Domain bleibt bei Ihnen. Wir übernehmen nur die Technik.'));

        $this->assertCount(1, $postfach->mails);
        $this->assertSame(self::KUNDE, $postfach->mails[0]['an']);
        $this->assertSame(Mailtexte::ANTWORT_BETREFF, $postfach->mails[0]['betreff']);
        $this->assertStringContainsString('Ihre Domain bleibt bei Ihnen.', $postfach->mails[0]['text']);
    }

    /** Zweimal beantworten geht nicht — und schickt keine zweite Mail. */
    public function testEineNachrichtLaesstSichNurEinmalBeantworten(): void
    {
        $nachrichtId = $this->nachrichtAnlegen();
        $postfach = new Postfach();
        $this->alsAdmin($this->adminId);
        $dienst = new \Sartu\Services\Nachrichtendienst($this->nachweis(), mail: $postfach);

        $this->assertSame([], $dienst->beantworten($nachrichtId, 'Die erste Antwort steht.'));
        $this->assertNotSame([], $dienst->beantworten($nachrichtId, 'Und noch eine hinterher.'));
        $this->assertCount(1, $postfach->mails);

        // Die erste Antwort steht unverändert in der Zeile.
        $anweisung = $this->pdo->prepare('SELECT answer_text FROM support_messages WHERE id = ?');
        $anweisung->execute([$nachrichtId]);

        $this->assertSame('Die erste Antwort steht.', (string) $anweisung->fetchColumn());
    }

    /** Eine zu kurze Antwort geht nicht raus — §8.9 verlangt mindestens zehn Zeichen. */
    public function testEineZuKurzeAntwortGehtNichtRaus(): void
    {
        $nachrichtId = $this->nachrichtAnlegen();
        $postfach = new Postfach();
        $this->alsAdmin($this->adminId);

        $fehler = (new \Sartu\Services\Nachrichtendienst($this->nachweis(), mail: $postfach))
            ->beantworten($nachrichtId, 'Ja.');

        $this->assertNotSame([], $fehler);
        $this->assertSame([], $postfach->mails);
    }

    // ---------------------------------------------------------------- 6 Angebot läuft ab

    /**
     * §10 — `Ihr Angebot gilt noch bis {Datum}`, drei Tage vorher, **einmal**.
     *
     * Der Lauf setzte abgelaufene Angebote schon auf `abgelaufen`, warnte aber nicht vorher:
     * Der Kunde erfuhr vom Ablauf, indem der Annahmeknopf verschwand.
     *
     * Geprüft wird die Grenze selbst, nicht ein Beispiel darin — vier Tage sind zu früh,
     * zwei zu spät. Ein Merker ohne Stichtag hätte an drei Tagen hintereinander geschickt.
     */
    public function testDieAblauferinnerungKommtDreiTageVorherUndGenauEinmal(): void
    {
        $postfach = new Postfach();
        $this->alsAdmin($this->adminId);
        $angebotId = $this->angebotAnlegen($postfach);
        (new AngebotDienst($this->nachweis(), mail: $postfach))->senden($angebotId, null);

        // Vier Tage: zu früh.
        $this->gueltigBis($angebotId, 4);
        $postfach = new Postfach();
        $this->assertSame(0, (new \Sartu\Services\Zahlungslauf(mail: $postfach))->ausfuehren()['ablauf']);
        $this->assertSame([], $postfach->mails);

        // Zwei Tage: zu spät, der eine Tag ist vorbei.
        $this->gueltigBis($angebotId, 2);
        $postfach = new Postfach();
        $this->assertSame(0, (new \Sartu\Services\Zahlungslauf(mail: $postfach))->ausfuehren()['ablauf']);
        $this->assertSame([], $postfach->mails);

        // Drei Tage: genau jetzt.
        $this->gueltigBis($angebotId, \Sartu\Services\Zahlungslauf::ABLAUF_VORLAUF_TAGE);
        $postfach = new Postfach();

        $this->assertSame(1, (new \Sartu\Services\Zahlungslauf(mail: $postfach))->ausfuehren()['ablauf']);
        $this->assertCount(1, $postfach->mails);
        $this->assertSame(self::KUNDE, $postfach->mails[0]['an']);

        $gueltigBis = \Sartu\Helpers\Format::inTagen(\Sartu\Services\Zahlungslauf::ABLAUF_VORLAUF_TAGE);
        $this->assertSame(Mailtexte::ablaufBetreff($gueltigBis), $postfach->mails[0]['betreff']);
        $this->assertStringContainsString(
            \Sartu\Helpers\Format::datum($gueltigBis),
            $postfach->mails[0]['text'],
        );

        // Zweiter Lauf am selben Tag: nichts mehr. Der Merker steht.
        $zweites = new Postfach();
        $this->assertSame(0, (new \Sartu\Services\Zahlungslauf(mail: $zweites))->ausfuehren()['ablauf']);
        $this->assertSame([], $zweites->mails);
    }

    /** Ein abgelaufenes Angebot geht auf `abgelaufen` — derselbe Lauf, andere Aufgabe. */
    public function testDerselbeLaufSetztAbgelaufeneAngeboteWeiterhin(): void
    {
        $postfach = new Postfach();
        $this->alsAdmin($this->adminId);
        $angebotId = $this->angebotAnlegen($postfach);
        (new AngebotDienst($this->nachweis(), mail: $postfach))->senden($angebotId, null);

        $this->gueltigBis($angebotId, -1);
        $stand = (new \Sartu\Services\Zahlungslauf(mail: new Postfach()))->ausfuehren();

        $this->assertSame(1, $stand['angebote']);
        $this->assertSame(0, $stand['ablauf'], 'Ein abgelaufenes Angebot wird nicht mehr erinnert.');

        $anweisung = $this->pdo->prepare('SELECT status FROM offers WHERE id = ?');
        $anweisung->execute([$angebotId]);

        $this->assertSame('abgelaufen', (string) $anweisung->fetchColumn());
    }

    // ---------------------------------------------------------------- Hilfsmittel

    private function gueltigBis(string $angebotId, int $inTagen): void
    {
        $anweisung = $this->pdo->prepare(
            'UPDATE offers SET valid_until = ?, reminder_sent_at = NULL WHERE id = ?'
        );
        $anweisung->execute([\Sartu\Helpers\Format::inTagen($inTagen), $angebotId]);
    }

    /** @return array{an:string,betreff:string,text:string} */
    private function mailAn(Postfach $postfach, string $adresse): array
    {
        foreach ($postfach->mails as $mail) {
            if ($mail['an'] === $adresse) {
                return $mail;
            }
        }

        $this->fail('Keine Nachricht an ' . $adresse . '.');
    }

    /** @return array{an:string,betreff:string,text:string} */
    private function mailMitBetreff(Postfach $postfach, string $betreff): array
    {
        foreach ($postfach->mails as $mail) {
            if ($mail['betreff'] === $betreff) {
                return $mail;
            }
        }

        $this->fail('Keine Nachricht mit dem Betreff „' . $betreff . '".');
    }

    private function anzahlMitBetreff(Postfach $postfach, string $betreff): int
    {
        return count(array_filter(
            $postfach->mails,
            static fn (array $mail) => $mail['betreff'] === $betreff,
        ));
    }

    /** @return array<string,mixed> */
    private function annahmeeingabe(): array
    {
        $eingabe = ['accepted_name' => 'Erika Mustermann'];

        foreach (array_keys(\Sartu\Services\Angebotsannahme::BESTAETIGUNGEN) as $feld) {
            $eingabe[$feld] = '1';
        }

        return $eingabe;
    }

    /** Angebot anlegen, senden und annehmen — der Weg bis zur Anzahlungsrechnung. */
    private function angebotAnnehmen(Postfach $postfach): void
    {
        $this->alsAdmin($this->adminId);
        $angebotId = $this->angebotAnlegen($postfach);

        $this->assertSame([], (new AngebotDienst($this->nachweis(), mail: $postfach))
            ->senden($angebotId, null));

        $this->alsKunde($this->organisationId, $this->kundeId);

        $this->assertSame([], (new \Sartu\Services\Angebotsannahme($this->bereich(), mail: $postfach))
            ->annehmen($angebotId, $this->annahmeeingabe(), $this->kundeId, null));
    }

    private function anzahlungAnlegen(Postfach $postfach): string
    {
        $angelegt = (new \Sartu\Services\Rechnungsdienst($this->nachweis(), mail: $postfach))
            ->anlegen($this->projektId, [
                'number'    => 'RE-2026-001',
                'milestone' => 'anzahlung',
                'net_cents' => '100000',
            ], null);

        $this->assertSame([], $angelegt['fehler']);

        return (string) $angelegt['id'];
    }

    /** @return list<string> */
    private function offenePflichtaufgabenOhneFreigabe(): array
    {
        $anweisung = $this->pdo->prepare(
            "SELECT id FROM tasks WHERE project_id = ? AND status = 'offen'"
            . " AND required = 1 AND kind <> 'freigabe'"
        );
        $anweisung->execute([$this->projektId]);

        return $anweisung->fetchAll(\PDO::FETCH_COLUMN);
    }

    private function freigabeaufgabe(): string
    {
        $anweisung = $this->pdo->prepare(
            "SELECT id FROM tasks WHERE project_id = ? AND kind = 'freigabe' LIMIT 1"
        );
        $anweisung->execute([$this->projektId]);

        return (string) $anweisung->fetchColumn();
    }

    private function nachrichtAnlegen(): string
    {
        $id = Uuid::v4();

        $anweisung = $this->pdo->prepare(
            'INSERT INTO support_messages (id, organization_id, project_id, body, created_by_user_id)'
            . ' VALUES (?, ?, ?, ?, ?)'
        );
        $anweisung->execute([
            $id, $this->organisationId, $this->projektId,
            'Was passiert mit meiner Domain, wenn wir umziehen?', $this->kundeId,
        ]);

        return $id;
    }

    private function nachweis(): AdminNachweis
    {
        $nachweis = AdminNachweis::ausSitzung();

        $this->assertNotNull($nachweis);

        return $nachweis;
    }

    private function bereich(): KundenBereich
    {
        return KundenBereich::ausSitzung();
    }

    private function projektAnlegen(): string
    {
        $id = Uuid::v4();

        $anweisung = $this->pdo->prepare(
            'INSERT INTO projects (id, organization_id, title, package, included_feedback_rounds,'
            . ' protection_level, status) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([
            $id, $this->organisationId, 'Website Mustermann', 'wachstum', 2, 'm',
            Projektstatus::ANGEBOT_OFFEN,
        ]);

        return $id;
    }

    /**
     * Ein vollständiges Angebot im Zustand `entwurf`.
     *
     * Über den Dienst, nicht über ein `INSERT`: Die Vorbelegung bringt die zwei
     * BFSG-Pflichtabsätze mit, und ein von Hand zusammengesetztes Angebot fällt bei der
     * Prüfung durch — was beim Messlauf am 02.08.2026 genau so passiert ist.
     */
    private function angebotAnlegen(Postfach $postfach, string $nummer = 'AN-2026-001'): string
    {
        $dienst = new AngebotDienst($this->nachweis(), mail: $postfach);
        $werte = $dienst->vorbelegung('wachstum');

        $werte['number'] = $nummer;
        $werte['summary'] = 'Zusammenfassung.';
        $werte['sitemap'] = 'Start, Leistungen, Kontakt';
        $werte['scope_pages'] = 8;
        $werte['scope_words'] = 4000;

        $angelegt = $dienst->anlegen($this->projektId, $werte, null);

        $this->assertSame([], $angelegt['fehler']);

        return (string) $angelegt['id'];
    }

    private function betreiberdatenAnlegen(): void
    {
        $anweisung = $this->pdo->prepare(
            'INSERT INTO operator_settings (id, firmenname, strasse, plz, ort, land, email,'
            . ' benachrichtigung_email, steuernummer, inhaltlich_verantwortlich)'
            . ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([
            Uuid::v4(), 'Betreiber', 'Strasse 1', '01067', 'Ort', 'DE',
            'betreiber@example.org', self::BETREUER, '337/5804/1234', 'Verantwortlich',
        ]);
    }
}
