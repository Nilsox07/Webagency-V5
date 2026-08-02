<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\Admin\AdminAufgaben;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Admin\AdminRechnungen;
use Sartu\Data\Customer\KundenAufgaben;
use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\Customer\KundenDateien;
use Sartu\Data\Customer\KundenFreigaben;
use Sartu\Data\Customer\KundenRechnungen;
use Sartu\Data\Uuid;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Format;
use Sartu\Router;
use Sartu\Services\Angebotsannahme;
use Sartu\Services\Aufgabendienst;
use Sartu\Services\Projektstatus;
use Sartu\Services\Projektwechsel;
use Sartu\Services\Rechnungsdienst;
use Sartu\Services\Uploaddienst;
use Sartu\Services\Zahlungslauf;
use Sartu\Services\Zahlungsstatus;
use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Wartungsmodus;

/**
 * Die Strecke, an der Stufe A2 gemessen wird.
 *
 * `REIHENFOLGE.md`, „Fertig, wenn": *„Der Kunde nimmt das Angebot an, und der Weg führt über
 * Anzahlung, Aufgaben und Faktenfreigabe bis `produktion`. Eine Rechnung mit überschrittenem
 * `due_date` steht am nächsten Tag auf `ueberfaellig`."*
 *
 * Testfälle: 11 · 12 · 13 · 14 · 15 · 16 · 17 · 24 · 26 · 27 · 46 · 51 · 52 · 53a ·
 * 61 · 77 · 78 · 79
 *
 * **Fall 18 stand hier falsch.** Er verlangt `approvals` mit `kind = abnahme`; geprüft war
 * die Faktenfreigabe mit `kind = inhalte` — das ist Fall 27. `REIHENFOLGE.md` ordnet 18 der
 * Etappe A3 zu, wo die Abnahme entsteht. Der Fall steht jetzt dort, jeder genau einmal.
 */
final class AuftragsstreckeTest extends Datenbankfall
{
    private string $adminId;

    private string $organisationId;

    private string $projektId;

    private string $kundeId;

    private string $angebotId;

    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER = ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_HOST' => 'localhost'];
        $_POST = [];
        $_GET = [];

        touch($this->arbeitsverzeichnis . '/' . InstallationsSperre::DATEINAME);

        $this->adminId = $this->adminAnlegen();
        $this->organisationId = $this->organisationAnlegen('Mustermann Sanitär GmbH', 'erika@example.org');
        $this->kundeId = $this->kundeAnlegen($this->organisationId, 'erika@example.org');
        $this->projektId = $this->projektAnlegen();
        $this->angebotId = $this->angebotAnlegen();
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_GET = [];

        parent::tearDown();
    }

    // ---------------------------------------------------------------- die ganze Strecke

    public function testVonDerAnnahmeUeberDieAnzahlungBisZurProduktion(): void
    {
        // 1 — der Kunde nimmt an.
        $this->alsKunde($this->organisationId, $this->kundeId);

        $fehler = (new Angebotsannahme($this->bereich()))
            ->annehmen($this->angebotId, $this->annahmeeingabe(), $this->kundeId, '127.0.0.1');

        $this->assertSame([], $fehler);
        $this->assertSame(Projektstatus::ANGEBOT_ANGENOMMEN, $this->projektstatus());

        // Testfall 24 — Umfang, Schutzstufe und Korrekturrunden gehen ins Projekt.
        $projekt = $this->projekt();
        $this->assertSame('platzhirsch', (string) $projekt['package']);
        $this->assertSame('l', (string) $projekt['protection_level']);
        $this->assertSame(2, (int) $projekt['included_feedback_rounds']);

        // 2 — der Admin stellt die Anzahlungsrechnung.
        $this->alsAdmin($this->adminId);
        $dienst = new Rechnungsdienst($this->nachweis());

        $rechnung = $dienst->anlegen($this->projektId, [
            'number'    => 'RE-2026-001',
            'milestone' => 'anzahlung',
            'net_cents' => 395000,
        ], '127.0.0.1');

        $this->assertSame([], $rechnung['fehler']);
        $this->assertSame([], $dienst->senden((string) $rechnung['id'], '127.0.0.1'));
        $this->assertSame(Projektstatus::ZAHLUNG_OFFEN, $this->projektstatus());

        // 3 — der Zahlungseingang wird von Hand eingetragen, mit Grundlagentext.
        $brutto = (int) $this->rechnung((string) $rechnung['id'])['gross_cents'];

        $this->assertSame([], $dienst->zahlungEintragen(
            (string) $rechnung['id'],
            $brutto,
            'Mollie-Zahlung tr_testfall vom 04.08.2026',
            '127.0.0.1',
        ));

        $this->assertSame(Zahlungsstatus::BEZAHLT, (string) $this->rechnung((string) $rechnung['id'])['status']);
        $this->assertSame(Projektstatus::BRIEFING, $this->projektstatus());

        // Die Aufgabenliste entsteht mit dem Zahlungseingang (§8.3).
        $this->alsKunde($this->organisationId, $this->kundeId);
        $aufgaben = new KundenAufgaben($this->bereich());
        $this->assertGreaterThan(0, count($aufgaben->liste()));

        // 4 — der Kunde erledigt die Pflichtaufgaben.
        foreach ($aufgaben->liste() as $aufgabe) {
            if ((string) $aufgabe['kind'] === 'freigabe' || (int) $aufgabe['required'] === 0) {
                continue;
            }

            $this->assertSame([], (new Aufgabendienst($this->bereich()))->abschliessen(
                (string) $aufgabe['id'],
                ['answer_text' => 'Unsere Antwort dazu.'],
                $this->kundeId,
                '127.0.0.1',
            ));
        }

        $this->assertSame(0, $aufgaben->offenePflichtaufgaben($this->projektId));

        // 5 — die Faktenfreigabe. Testfall 27.
        $freigabeaufgabe = $this->freigabeaufgabe();

        $this->assertSame([], (new Aufgabendienst($this->bereich()))->abschliessen(
            $freigabeaufgabe,
            ['bestaetigung' => '1', 'granted_name' => 'Erika Mustermann'],
            $this->kundeId,
            '127.0.0.1',
        ));

        $this->assertSame(Projektstatus::PRODUKTION, $this->projektstatus());

        $freigabe = (new KundenFreigaben($this->bereich()))
            ->finden($this->projektId, KundenFreigaben::INHALTE);

        $this->assertNotNull($freigabe, 'Es gibt keinen Eintrag in approvals.');
        $this->assertSame('Erika Mustermann', (string) $freigabe['granted_name']);
        $this->assertNotNull($freigabe['granted_at'], 'Der Startzeitpunkt des Lieferkorridors fehlt.');
    }

    // ---------------------------------------------------------------- Angebotsannahme

    /** Testfall 11 — ohne alle vier Bestätigungen scheitert die Annahme. */
    public function testAnnahmeOhneAlleVierBestaetigungenScheitert(): void
    {
        $this->alsKunde($this->organisationId, $this->kundeId);

        foreach (array_keys(Angebotsannahme::BESTAETIGUNGEN) as $weggelassen) {
            $eingabe = $this->annahmeeingabe();
            unset($eingabe[$weggelassen]);

            $fehler = (new Angebotsannahme($this->bereich()))
                ->annehmen($this->angebotId, $eingabe, $this->kundeId, null);

            $this->assertNotSame([], $fehler, 'Ohne ' . $weggelassen . ' ging es durch.');
            $this->assertSame('gesendet', (string) $this->angebot()['status']);
        }

        $this->assertCount(4, Angebotsannahme::BESTAETIGUNGEN);
    }

    /** Testfall 11, zweite Hälfte — ohne getippten Namen ebenfalls nicht. */
    public function testAnnahmeOhneNamenScheitert(): void
    {
        $this->alsKunde($this->organisationId, $this->kundeId);

        $eingabe = $this->annahmeeingabe();
        $eingabe['accepted_name'] = '   ';

        $fehler = (new Angebotsannahme($this->bereich()))
            ->annehmen($this->angebotId, $eingabe, $this->kundeId, null);

        $this->assertContains('Bitte geben Sie Ihren Namen an.', $fehler);
    }

    /** Testfall 12 — ein angenommenes Angebot lässt sich nicht erneut annehmen. */
    public function testAngenommenesAngebotLaesstSichNichtErneutAnnehmen(): void
    {
        $this->alsKunde($this->organisationId, $this->kundeId);
        $dienst = new Angebotsannahme($this->bereich());

        $this->assertSame([], $dienst->annehmen($this->angebotId, $this->annahmeeingabe(), $this->kundeId, null));

        $zweiter = $dienst->annehmen($this->angebotId, $this->annahmeeingabe(), $this->kundeId, null);

        $this->assertNotSame([], $zweiter);
        $this->assertSame('Erika Mustermann', (string) $this->angebot()['accepted_name']);
    }

    /** Testfall 13 — ein abgelaufenes Angebot lässt sich nicht annehmen. */
    public function testAbgelaufenesAngebotLaesstSichNichtAnnehmen(): void
    {
        $anweisung = $this->pdo->prepare('UPDATE offers SET valid_until = ? WHERE id = ?');
        $anweisung->execute(['2020-01-01', $this->angebotId]);

        $this->alsKunde($this->organisationId, $this->kundeId);

        $fehler = (new Angebotsannahme($this->bereich()))
            ->annehmen($this->angebotId, $this->annahmeeingabe(), $this->kundeId, null);

        $this->assertNotSame([], $fehler);
        $this->assertStringContainsString('abgelaufen', implode(' ', $fehler));
        $this->assertSame('gesendet', (string) $this->angebot()['status']);
    }

    // ---------------------------------------------------------------- Zahlungen

    /**
     * Testfall 14 — der Rechnungsstatus wechselt nicht durch den Aufruf einer Rückkehr-URL.
     *
     * Geprüft wird strenger, als der Fall verlangt: Es gibt **keine** Route, die eine
     * Rückkehr entgegennehmen könnte, und **keine** Methode im Dienst, die einen Zustand
     * ohne Betrag und Grundlagentext setzt. Ein Test, der nur eine erfundene Adresse
     * aufruft, prüft das Fehlen nicht.
     */
    public function testKeineRueckkehrUrlKannDenZahlungsstatusAendern(): void
    {
        $rechnungId = $this->rechnungAnlegen();
        $this->alsKunde($this->organisationId, $this->kundeId);

        // Keine Route nimmt eine Rückkehr entgegen.
        foreach ([
            '/portal/rechnungen?zahlung=erfolgreich',
            '/portal/rechnungen/' . $rechnungId . '/bezahlt',
            '/api/mollie',
            '/portal/zahlung/rueckkehr',
        ] as $pfad) {
            $antwort = $this->router()->behandeln('GET', $pfad);

            $this->assertNotSame(200, $antwort->status, $pfad . ' wurde beantwortet.');
        }

        $_GET = ['zahlung' => 'erfolgreich', 'status' => 'paid'];
        $this->router()->behandeln('GET', '/portal/rechnungen');
        $_GET = [];

        $this->assertSame('gesendet', (string) $this->rechnung($rechnungId)['status']);
        $this->assertSame(0, (int) $this->rechnung($rechnungId)['paid_cents']);

        // Und im Dienst gibt es keine Methode, die ohne Grundlagentext buchen würde.
        $this->alsAdmin($this->adminId);
        $fehler = (new Rechnungsdienst($this->nachweis()))
            ->zahlungEintragen($rechnungId, 119000, '', null);

        $this->assertNotSame([], $fehler);
        $this->assertSame('gesendet', (string) $this->rechnung($rechnungId)['status']);
    }

    /** §12: Ohne Grundlagentext lässt sich keine Änderung an Geld speichern. */
    public function testZuKurzerGrundlagentextWirdAbgewiesen(): void
    {
        $rechnungId = $this->rechnungAnlegen();
        $this->alsAdmin($this->adminId);

        $fehler = (new Rechnungsdienst($this->nachweis()))
            ->zahlungEintragen($rechnungId, 119000, 'ok', null);

        $this->assertNotSame([], $fehler);
        $this->assertSame(Rechnungsdienst::GRUNDLAGE_MINDESTLAENGE, 3);
    }

    /** Testfall 77 — eine Teilzahlung ergibt `teilweise_bezahlt`, nicht `bezahlt`. */
    public function testTeilzahlungErgibtTeilweiseBezahlt(): void
    {
        $rechnungId = $this->rechnungAnlegen();
        $this->alsAdmin($this->adminId);

        (new Rechnungsdienst($this->nachweis()))
            ->zahlungEintragen($rechnungId, 60000, 'Überweisung Kontoauszug 12/2026', null);

        $rechnung = $this->rechnung($rechnungId);

        $this->assertSame(Zahlungsstatus::TEILWEISE_BEZAHLT, (string) $rechnung['status']);
        $this->assertSame(60000, (int) $rechnung['paid_cents']);
        $this->assertNull($rechnung['paid_at'], 'paid_at wurde bei einer Teilzahlung gesetzt.');
        $this->assertSame(59000, Zahlungsstatus::restbetrag(60000, 119000));
    }

    /** §4: Überzahlung wird nicht abgewiesen, sondern gespeichert. */
    public function testUeberzahlungWirdGespeichertUndAngezeigt(): void
    {
        $rechnungId = $this->rechnungAnlegen();
        $this->alsAdmin($this->adminId);

        $this->assertSame([], (new Rechnungsdienst($this->nachweis()))
            ->zahlungEintragen($rechnungId, 130000, 'Überweisung mit Aufschlag', null));

        $rechnung = $this->rechnung($rechnungId);

        $this->assertSame(Zahlungsstatus::BEZAHLT, (string) $rechnung['status']);
        $this->assertSame(130000, (int) $rechnung['paid_cents']);
        $this->assertSame(11000, Zahlungsstatus::ueberzahlung(130000, 119000));
    }

    /** §12: Jede Buchung erzeugt ein Audit-Ereignis mit dem Grundlagentext als `reason`. */
    public function testBuchungErzeugtAuditMitGrundlagentext(): void
    {
        $rechnungId = $this->rechnungAnlegen();
        $this->alsAdmin($this->adminId);

        (new Rechnungsdienst($this->nachweis()))
            ->zahlungEintragen($rechnungId, 119000, 'Mollie-Zahlung tr_abc vom 04.08.2026', '127.0.0.1');

        $ereignis = $this->letztesEreignis('zahlungsstatus_geaendert');

        $this->assertSame($this->adminId, (string) $ereignis['actor_user_id']);
        $this->assertSame('invoice', (string) $ereignis['entity_type']);
        $this->assertSame($rechnungId, (string) $ereignis['entity_id']);
        $this->assertSame('gesendet', (string) $ereignis['old_value']);
        $this->assertSame('bezahlt', (string) $ereignis['new_value']);
        $this->assertSame('Mollie-Zahlung tr_abc vom 04.08.2026', (string) $ereignis['reason']);
        $this->assertSame('127.0.0.1', (string) $ereignis['ip']);
    }

    // ---------------------------------------------------------------- die täglichen Läufe

    /** Testfall 15 — `ueberfaellig` wird gesetzt, wenn `due_date` überschritten ist. */
    public function testUeberfaelligWirdAmNaechstenTagGesetzt(): void
    {
        $rechnungId = $this->rechnungAnlegen();
        $this->faelligkeitVerschieben($rechnungId, '-1 day');

        $stand = (new Zahlungslauf())->ausfuehren();

        $this->assertSame(1, $stand['ueberfaellig']);
        $this->assertSame(Zahlungsstatus::UEBERFAELLIG, (string) $this->rechnung($rechnungId)['status']);

        // Eine bezahlte Rechnung wird nicht überfällig.
        $bezahlt = $this->rechnungAnlegen('RE-2026-002');
        $this->faelligkeitVerschieben($bezahlt, '-1 day');
        $this->alsAdmin($this->adminId);
        (new Rechnungsdienst($this->nachweis()))->zahlungEintragen($bezahlt, 119000, 'Kontoauszug', null);

        $this->assertSame(0, (new Zahlungslauf())->ausfuehren()['ueberfaellig']);
        $this->assertSame(Zahlungsstatus::BEZAHLT, (string) $this->rechnung($bezahlt)['status']);
    }

    /**
     * §5.3 — `teilweise_bezahlt` und `ueberfaellig` schließen sich nicht aus.
     *
     * Der Zustand trägt die Frist, der Anzeigetext trägt beides.
     */
    public function testAngezahlteRechnungNachFaelligkeitIstBeides(): void
    {
        $rechnungId = $this->rechnungAnlegen();
        $this->alsAdmin($this->adminId);
        (new Rechnungsdienst($this->nachweis()))->zahlungEintragen($rechnungId, 60000, 'Teilzahlung', null);

        $this->faelligkeitVerschieben($rechnungId, '-1 day');
        (new Zahlungslauf())->ausfuehren();

        $rechnung = $this->rechnung($rechnungId);

        $this->assertSame(Zahlungsstatus::UEBERFAELLIG, (string) $rechnung['status']);
        $this->assertSame(60000, (int) $rechnung['paid_cents']);

        $text = Zahlungsstatus::kundentext($rechnung);

        $this->assertStringContainsString('Überfällig seit', $text);
        $this->assertStringContainsString('offen: 590,00 €', $text);
    }

    /** Testfall 78 — die Zahlungserinnerung geht genau einmal raus, nicht täglich. */
    public function testZahlungserinnerungGehtGenauEinmalRaus(): void
    {
        $rechnungId = $this->rechnungAnlegen();
        $this->faelligkeitVerschieben($rechnungId, '-1 day');

        $this->assertSame(1, (new Zahlungslauf())->ausfuehren()['erinnerung1']);
        $this->assertNotNull($this->rechnung($rechnungId)['reminder_sent_at']);

        // Zweiter Lauf am selben Tag: nichts mehr.
        $this->assertSame(0, (new Zahlungslauf())->ausfuehren()['erinnerung1']);

        // Sechs Tage später ist die zweite noch nicht dran.
        $this->erinnerungVerschieben($rechnungId, 6);
        $this->assertSame(0, (new Zahlungslauf())->ausfuehren()['erinnerung2']);

        // Nach sieben Tagen genau einmal.
        $this->erinnerungVerschieben($rechnungId, 7);
        $this->assertSame(1, (new Zahlungslauf())->ausfuehren()['erinnerung2']);
        $this->assertSame(0, (new Zahlungslauf())->ausfuehren()['erinnerung2'], 'Die zweite kam ein zweites Mal.');

        // Und danach keine weitere — „ab hier entscheidet ein Mensch".
        $this->erinnerungVerschieben($rechnungId, 30);
        $stand = (new Zahlungslauf())->ausfuehren();
        $this->assertSame(0, $stand['erinnerung1']);
        $this->assertSame(0, $stand['erinnerung2']);
    }

    /** §5.2: Ein abgelaufenes Angebot wird vom Lauf auf `abgelaufen` gesetzt. */
    public function testAbgelaufenesAngebotWirdVomLaufGesetzt(): void
    {
        $anweisung = $this->pdo->prepare('UPDATE offers SET valid_until = ? WHERE id = ?');
        $anweisung->execute(['2020-01-01', $this->angebotId]);

        $this->assertSame(1, (new Zahlungslauf())->ausfuehren()['angebote']);
        $this->assertSame('abgelaufen', (string) $this->angebot()['status']);
    }

    // ---------------------------------------------------------------- §8.1 Block 3

    /**
     * Die Schwelle für „knappe Frist" — **drei Tage**, entschieden am 02.08.2026.
     *
     * Geprüft wird die Grenze selbst, nicht ein Beispiel darin: drei Tage zählen noch, vier
     * nicht mehr. Eine Schwelle, die nur in der Mitte geprüft ist, ist nicht geprüft.
     */
    public function testKnappeFristBeginntDreiTageVorFaelligkeit(): void
    {
        $this->assertSame(3, Zahlungsstatus::KNAPP_TAGE);

        $offen = static fn (int $tage, string $zustand = Zahlungsstatus::GESENDET): array => [
            'status' => $zustand, 'due_date' => Format::inTagen($tage),
        ];

        $this->assertTrue(Zahlungsstatus::fristKnapp($offen(3)), 'Drei Tage zählen noch.');
        $this->assertFalse(Zahlungsstatus::fristKnapp($offen(4)), 'Vier Tage sind nicht knapp.');
        $this->assertTrue(Zahlungsstatus::fristKnapp($offen(0)), 'Heute fällig ist knapp.');

        // Gestern fällig ist nicht knapp, sondern vorbei — das sagt der Zustand.
        $this->assertFalse(Zahlungsstatus::fristKnapp($offen(-1)));
        $this->assertFalse(Zahlungsstatus::fristKnapp($offen(1, Zahlungsstatus::UEBERFAELLIG)));

        // Eine angezahlte Rechnung hat weiter eine Frist, eine bezahlte nicht mehr.
        $this->assertTrue(Zahlungsstatus::fristKnapp($offen(2, Zahlungsstatus::TEILWEISE_BEZAHLT)));
        $this->assertFalse(Zahlungsstatus::fristKnapp($offen(2, Zahlungsstatus::BEZAHLT)));
        $this->assertFalse(Zahlungsstatus::fristKnapp($offen(2, Zahlungsstatus::STORNIERT)));

        // Ohne Fälligkeit gibt es nichts zu warnen.
        $this->assertFalse(Zahlungsstatus::fristKnapp(['status' => Zahlungsstatus::GESENDET]));
    }

    /** §8.1 Block 3 — die offenen Punkte stehen im Cockpit, mit Link und knappem Hinweis. */
    public function testCockpitZeigtOffenePunkteMitHinweisBeiKnapperFrist(): void
    {
        $rechnungId = $this->rechnungAnlegen();
        $this->faelligkeitVerschieben($rechnungId, '+2 days');
        $this->aufgabeAnlegen('angabe');

        $this->alsKunde($this->organisationId, $this->kundeId);
        $rumpf = $this->router()->behandeln('GET', '/portal')->rumpf;

        $this->assertStringContainsString('Offene Punkte', $rumpf);
        $this->assertStringContainsString('Eine offene Aufgabe', $rumpf);
        $this->assertStringContainsString('/portal/aufgaben', $rumpf);
        $this->assertStringContainsString(
            'Rechnung RE-2026-001 — zahlbar bis ' . Format::datum(Format::inTagen(2)),
            $rumpf
        );
        $this->assertStringContainsString('Diese Frist ist in wenigen Tagen erreicht.', $rumpf);
    }

    /** Ohne knappe Frist steht der Hinweis nicht da — und ohne offenen Punkt kein Block. */
    public function testCockpitOhneKnappeFristUndOhneOffenePunkte(): void
    {
        $rechnungId = $this->rechnungAnlegen();
        $this->faelligkeitVerschieben($rechnungId, '+10 days');

        $this->alsKunde($this->organisationId, $this->kundeId);
        $rumpf = $this->router()->behandeln('GET', '/portal')->rumpf;

        $this->assertStringContainsString('Rechnung RE-2026-001', $rumpf);
        $this->assertStringNotContainsString('Diese Frist ist in wenigen Tagen erreicht.', $rumpf);

        // Bezahlt: kein offener Punkt, kein Kasten.
        $this->alsAdmin($this->adminId);
        (new Rechnungsdienst($this->nachweis()))->zahlungEintragen($rechnungId, 119000, 'Kontoauszug', null);

        $this->alsKunde($this->organisationId, $this->kundeId);
        $rumpf = $this->router()->behandeln('GET', '/portal')->rumpf;

        $this->assertStringNotContainsString('Offene Punkte', $rumpf);
    }

    // ---------------------------------------------------------------- §8.1 Block 4

    /**
     * §8.1 Block 4 — die letzten fünf Ereignisse in Klartext, mit Datum.
     *
     * **Genau die fünf aus §8.1, keine sechste.** Geprüft wird deshalb beides: dass die
     * genannten erscheinen **und** dass ein Ereignis daneben — hier `rechnung_gesendet` und
     * `angebot_gesendet` — nicht erscheint. Ein Test, der nur die Treffer zählt, würde eine
     * zu weite Bedingung nicht bemerken.
     */
    public function testDasCockpitZeigtNurDieFuenfEreignisseAusAchtEinsUndKeinesDaneben(): void
    {
        $this->alsKunde($this->organisationId, $this->kundeId);
        (new Angebotsannahme($this->bereich()))
            ->annehmen($this->angebotId, $this->annahmeeingabe(), $this->kundeId, null);

        $rechnungId = $this->rechnungAnlegen();
        $this->alsAdmin($this->adminId);
        (new Rechnungsdienst($this->nachweis()))->senden($rechnungId, null);
        (new Rechnungsdienst($this->nachweis()))
            ->zahlungEintragen($rechnungId, 119000, 'Kontoauszug', null);

        $this->alsKunde($this->organisationId, $this->kundeId);
        $rumpf = $this->router()->behandeln('GET', '/portal')->rumpf;

        $this->assertStringContainsString('Letzte Aktivität', $rumpf);
        $this->assertStringContainsString('Angebot angenommen', $rumpf);
        $this->assertStringContainsString('Zahlung eingegangen', $rumpf);

        // Kein Systemcode, keine Begründung, keine IP — §3 Regel 12.
        $this->assertStringNotContainsString('zahlungsstatus_geaendert', $rumpf);
        $this->assertStringNotContainsString('Kontoauszug', $rumpf);
        $this->assertStringNotContainsString('127.0.0.1', $rumpf);

        // Und die Klartexte, die §8.1 nicht nennt, stehen nicht da.
        $this->assertStringNotContainsString('Rechnung gesendet', $rumpf);
        $this->assertStringNotContainsString('Angebot gesendet', $rumpf);
    }

    /**
     * Höchstens fünf — und nur die eigene Organisation.
     *
     * Der Filter kommt aus der Sitzung (§3 Regel 1). Ein Ereignis einer fremden Organisation
     * darf nicht auftauchen, auch wenn es dieselbe Aktion trägt.
     */
    public function testBlockVierZeigtHoechstensFuenfUndNichtsFremdes(): void
    {
        $fremd = $this->organisationAnlegen('Fremdbetrieb GmbH', 'fremd@example.org');

        for ($i = 0; $i < 7; ++$i) {
            $this->ereignisAnlegen($this->organisationId, 'angebot_angenommen', null);
        }

        $this->ereignisAnlegen($fremd, 'angebot_angenommen', null);

        $this->alsKunde($this->organisationId, $this->kundeId);
        $eintraege = (new \Sartu\Data\Customer\KundenAktivitaet($this->bereich()))->letzte();

        $this->assertCount(\Sartu\Data\Customer\KundenAktivitaet::ANZAHL, $eintraege);

        foreach ($eintraege as $eintrag) {
            $this->assertSame('Angebot angenommen', $eintrag['text']);
        }

        // Gegenprobe: Die fremde Organisation sieht ihr eigenes Ereignis — und nur das.
        $fremderKunde = $this->kundeAnlegen($fremd, 'fremd@example.org');
        $this->alsKunde($fremd, $fremderKunde);

        $this->assertCount(1, (new \Sartu\Data\Customer\KundenAktivitaet($this->bereich()))->letzte());
    }

    // ---------------------------------------------------------------- Aufgaben und Uploads

    /** Testfall 16 — eine Aufgabe mit Pflichtantwort lässt sich nicht ohne Antwort abschließen. */
    public function testAufgabeMitPflichtantwortBrauchtEineAntwort(): void
    {
        $this->alsKunde($this->organisationId, $this->kundeId);
        $aufgabeId = $this->aufgabeAnlegen('angabe');

        $fehler = (new Aufgabendienst($this->bereich()))
            ->abschliessen($aufgabeId, ['answer_text' => '   '], $this->kundeId, null);

        $this->assertContains('Bitte beantworten Sie die Frage, bevor Sie die Aufgabe abschließen.', $fehler);
        $this->assertSame('offen', (string) $this->aufgabe($aufgabeId)['status']);
    }

    /** Testfall 17 — ein Upload ohne Rechtebestätigung wird abgelehnt. */
    public function testUploadOhneRechtebestaetigungWirdAbgelehnt(): void
    {
        $this->alsKunde($this->organisationId, $this->kundeId);
        $aufgabeId = $this->aufgabeAnlegen('upload');

        $ergebnis = (new Uploaddienst($this->bereich()))
            ->annehmen($this->testdatei('bild.png'), $aufgabeId, false, $this->kundeId);

        $this->assertSame('Bitte bestätigen Sie die Bildrechte.', $ergebnis['fehler']);
        $this->assertSame(0, (new KundenDateien($this->bereich()))->anzahlJeAufgabe($aufgabeId));
    }

    /** Testfall 46 — ein unerlaubter Dateityp wird abgelehnt, auch mit passender Endung. */
    public function testUnerlaubterDateitypWirdAbgelehnt(): void
    {
        $this->alsKunde($this->organisationId, $this->kundeId);
        $aufgabeId = $this->aufgabeAnlegen('upload');
        $dienst = new Uploaddienst($this->bereich());

        // Endung erlaubt, Inhalt nicht: eine ausführbare Datei mit der Endung .png.
        $getarnt = $this->testdatei('bild.png', "#!/bin/sh\necho hallo\n");

        $this->assertNotNull($dienst->annehmen($getarnt, $aufgabeId, true, $this->kundeId)['fehler']);

        // Endung nicht erlaubt.
        $this->assertNotNull($dienst->annehmen($this->testdatei('schad.exe'), $aufgabeId, true, $this->kundeId)['fehler']);

        // Und der erlaubte Fall geht durch — sonst prüft der Test nur, dass nichts geht.
        $this->assertNull($dienst->annehmen($this->testdatei('bild.png'), $aufgabeId, true, $this->kundeId)['fehler']);
        $this->assertSame(1, (new KundenDateien($this->bereich()))->anzahlJeAufgabe($aufgabeId));
    }

    /** Testfall 79 — die Speichergrenze je Organisation greift. */
    public function testSpeichergrenzeJeOrganisationGreift(): void
    {
        $this->alsKunde($this->organisationId, $this->kundeId);
        $aufgabeId = $this->aufgabeAnlegen('upload');

        // Eine Zeile, die den Speicher bereits fast füllt — ohne 500 MB zu schreiben.
        $anweisung = $this->pdo->prepare(
            'INSERT INTO task_files (id, task_id, organization_id, original_name, stored_name,'
            . ' mime_type, size_bytes, rights_confirmed) VALUES (?, ?, ?, ?, ?, ?, ?, 1)'
        );
        $anweisung->execute([
            Uuid::v4(), $aufgabeId, $this->organisationId, 'gross.zip', Uuid::v4(),
            'application/zip', Uploaddienst::MAX_BYTES_JE_ORGANISATION - 10,
        ]);

        $ergebnis = (new Uploaddienst($this->bereich()))
            ->annehmen($this->testdatei('bild.png'), $aufgabeId, true, $this->kundeId);

        $this->assertStringContainsString('Ihr Speicher ist voll (500 MB)', (string) $ergebnis['fehler']);
    }

    /** Testfall 26 — die Freigabeaufgabe ist gesperrt, solange Pflichtaufgaben offen sind. */
    public function testFreigabeIstGesperrtSolangePflichtaufgabenOffenSind(): void
    {
        $this->alsKunde($this->organisationId, $this->kundeId);

        $offene = $this->aufgabeAnlegen('angabe', pflicht: true);
        $freigabe = $this->aufgabeAnlegen('freigabe', pflicht: true);

        $fehler = (new Aufgabendienst($this->bereich()))->abschliessen(
            $freigabe,
            ['bestaetigung' => '1', 'granted_name' => 'Erika Mustermann'],
            $this->kundeId,
            null,
        );

        $this->assertContains('Bitte schließen Sie zuerst die noch offenen Aufgaben ab.', $fehler);
        $this->assertNull((new KundenFreigaben($this->bereich()))->finden($this->projektId, 'inhalte'));

        // Nach dem Erledigen geht es.
        (new Aufgabendienst($this->bereich()))
            ->abschliessen($offene, ['answer_text' => 'Antwort'], $this->kundeId, null);

        $this->assertSame([], (new Aufgabendienst($this->bereich()))->abschliessen(
            $freigabe,
            ['bestaetigung' => '1', 'granted_name' => 'Erika Mustermann'],
            $this->kundeId,
            null,
        ));
    }

    /** Testfall 27 — die Faktenfreigabe erzeugt `approvals` mit `kind = inhalte` **und** ein Audit-Ereignis. */
    public function testFreigabeErzeugtApprovalUndAudit(): void
    {
        $this->projektStatusSetzen(Projektstatus::BRIEFING);
        $this->alsKunde($this->organisationId, $this->kundeId);

        $freigabe = $this->aufgabeAnlegen('freigabe', pflicht: true);

        (new Aufgabendienst($this->bereich()))->abschliessen(
            $freigabe,
            ['bestaetigung' => '1', 'granted_name' => 'Erika Mustermann'],
            $this->kundeId,
            '127.0.0.1',
        );

        $eintrag = (new KundenFreigaben($this->bereich()))->finden($this->projektId, 'inhalte');

        $this->assertNotNull($eintrag);
        $this->assertSame('127.0.0.1', (string) $eintrag['granted_ip']);

        $ereignis = $this->letztesEreignis('faktenfreigabe_erteilt');
        $this->assertSame($this->kundeId, (string) $ereignis['actor_user_id']);
        $this->assertStringContainsString('Erika Mustermann', (string) $ereignis['reason']);

        // §4: „Eine Erklärung ist einmalig."
        $zweite = (new KundenFreigaben($this->bereich()))
            ->erklaeren($this->projektId, 'inhalte', $this->kundeId, 'Jemand anders', null);

        $this->assertFalse($zweite);
        $this->assertSame('Erika Mustermann', (string) $eintrag['granted_name']);
    }

    // ---------------------------------------------------------------- Übergangstabelle

    /** Testfall 61 / 60 — `zahlung_offen` lässt sich nicht überspringen. */
    public function testProduktionBeginntNichtOhneZahlungseingang(): void
    {
        $this->projektStatusSetzen(Projektstatus::ZAHLUNG_OFFEN);

        $fehler = (new Projektwechsel())->wechseln(
            $this->projektId,
            $this->organisationId,
            Projektstatus::PRODUKTION,
            Projektstatus::ADMIN,
            $this->adminId,
            'Wir fangen schon mal an',
            null,
        );

        $this->assertNotNull($fehler);
        $this->assertSame(Projektstatus::ZAHLUNG_OFFEN, $this->projektstatus());

        // Kein Teileffekt: auch kein Audit-Ereignis.
        $anweisung = $this->pdo->prepare(
            "SELECT COUNT(*) FROM audit_events WHERE action = 'projektstatus_geaendert' AND entity_id = ?"
        );
        $anweisung->execute([$this->projektId]);

        $this->assertSame(0, (int) $anweisung->fetchColumn());
    }

    /** §5.1a: Ein Kunde kann seinen eigenen Zahlungseingang nicht bestätigen. */
    public function testKundeKannDenZahlungseingangNichtSelbstBestaetigen(): void
    {
        $this->projektStatusSetzen(Projektstatus::ZAHLUNG_OFFEN);

        $fehler = (new Projektwechsel())->wechseln(
            $this->projektId,
            $this->organisationId,
            Projektstatus::BRIEFING,
            Projektstatus::KUNDE,
            $this->kundeId,
            'Ich habe überwiesen',
            null,
        );

        $this->assertSame('Dieser Schritt liegt nicht bei Ihnen.', $fehler);
        $this->assertSame(Projektstatus::ZAHLUNG_OFFEN, $this->projektstatus());
    }

    /** §5.1a: Ein Wechsel an Geld und Fristen ohne Grund wird abgewiesen. */
    public function testWechselAnGeldUndFristenBrauchtEinenGrund(): void
    {
        $this->projektStatusSetzen(Projektstatus::ZAHLUNG_OFFEN);

        $fehler = (new Projektwechsel())->wechseln(
            $this->projektId,
            $this->organisationId,
            Projektstatus::BRIEFING,
            Projektstatus::ADMIN,
            $this->adminId,
            '   ',
            null,
        );

        $this->assertNotNull($fehler);
        $this->assertSame(Projektstatus::ZAHLUNG_OFFEN, $this->projektstatus());
    }

    /** §3 Regel 1: Ein Wechsel an einem fremden Projekt findet das Projekt nicht. */
    public function testWechselAnFremdemProjektFindetNichts(): void
    {
        $fremde = $this->organisationAnlegen('Betrieb B', 'b@example.org');

        $fehler = (new Projektwechsel())->wechseln(
            $this->projektId,
            $fremde,
            Projektstatus::ANGEBOT_ANGENOMMEN,
            Projektstatus::KUNDE,
            $this->kundeId,
            null,
            null,
        );

        $this->assertSame('Dieses Projekt gibt es nicht.', $fehler);
        $this->assertSame(Projektstatus::ANGEBOT_OFFEN, $this->projektstatus());
    }

    // ---------------------------------------------------------------- Hilfsmittel

    private function bereich(): KundenBereich
    {
        return KundenBereich::ausSitzung();
    }

    private function nachweis(): AdminNachweis
    {
        $nachweis = AdminNachweis::ausSitzung();

        $this->assertNotNull($nachweis);

        return $nachweis;
    }

    /** @return array<string,mixed> */
    private function annahmeeingabe(): array
    {
        $eingabe = ['accepted_name' => 'Erika Mustermann'];

        foreach (array_keys(Angebotsannahme::BESTAETIGUNGEN) as $feld) {
            $eingabe[$feld] = '1';
        }

        return $eingabe;
    }

    private function projektAnlegen(): string
    {
        $id = Uuid::v4();

        $anweisung = $this->pdo->prepare(
            'INSERT INTO projects (id, organization_id, title, package, included_feedback_rounds,'
            . ' protection_level, status) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([$id, $this->organisationId, 'Website Mustermann', 'wachstum', 2, 'm',
            Projektstatus::ANGEBOT_OFFEN]);

        return $id;
    }

    private function angebotAnlegen(): string
    {
        $id = Uuid::v4();

        $anweisung = $this->pdo->prepare(
            'INSERT INTO offers (id, project_id, number, status, package, summary, sitemap, inclusions,'
            . ' exclusions, scope_pages, included_feedback_rounds, delivery_days_min, delivery_days_max,'
            . ' delivery_start_condition, one_time_net_cents, protection_level,'
            . ' protection_monthly_net_cents, protection_min_term_months, first_year_net_cents,'
            . ' payment_plan, rights_text, domain_text, valid_until)'
            . ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([
            $id, $this->projektId, 'AN-2026-001', 'gesendet', 'platzhirsch', 'Zusammenfassung',
            'Seitenstruktur', 'Enthalten', 'Nicht enthalten', 16, 2, 15, 25, 'Bedingung',
            790000, 'l', 24900, 12, 790000 + 12 * 24900, '50_50', 'Rechte', 'Domain', '2099-12-31',
        ]);

        return $id;
    }

    private function rechnungAnlegen(string $nummer = 'RE-2026-001'): string
    {
        $id = Uuid::v4();

        $anweisung = $this->pdo->prepare(
            'INSERT INTO invoices (id, project_id, number, milestone, status, net_cents, vat_cents,'
            . ' gross_cents, due_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([
            $id, $this->projektId, $nummer, 'anzahlung', 'gesendet', 100000, 19000, 119000,
            (new \DateTimeImmutable('now', new \DateTimeZone('Europe/Berlin')))->modify('+10 days')->format('Y-m-d'),
        ]);

        return $id;
    }

    private function aufgabeAnlegen(string $art, bool $pflicht = true): string
    {
        $this->alsAdmin($this->adminId);
        $id = (new AdminAufgaben($this->nachweis(), $this->pdo))->anlegen([
            'project_id' => $this->projektId,
            'title'      => 'Aufgabe ' . $art,
            'kind'       => $art,
            'status'     => 'offen',
            'sort_order' => 1,
            'required'   => $pflicht ? 1 : 0,
        ]);

        $this->alsKunde($this->organisationId, $this->kundeId);

        return $id;
    }

    /** @return array<string,mixed> Ein Eintrag, wie ihn `$_FILES` liefert. */
    private function testdatei(string $name, ?string $inhalt = null): array
    {
        $pfad = $this->arbeitsverzeichnis . '/' . bin2hex(random_bytes(4)) . '-' . $name;

        // Ein echtes PNG, sonst erkennt `finfo` es nicht als solches.
        $inhalt ??= base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        );

        file_put_contents($pfad, $inhalt);

        return [
            'name'     => $name,
            'tmp_name' => $pfad,
            'size'     => filesize($pfad),
            'error'    => UPLOAD_ERR_OK,
            'type'     => 'image/png',
        ];
    }

    private function projektStatusSetzen(string $status): void
    {
        $anweisung = $this->pdo->prepare('UPDATE projects SET status = ? WHERE id = ?');
        $anweisung->execute([$status, $this->projektId]);
    }

    private function projektstatus(): string
    {
        return (string) $this->projekt()['status'];
    }

    /** @return array<string,mixed> */
    private function projekt(): array
    {
        $anweisung = $this->pdo->prepare('SELECT * FROM projects WHERE id = ?');
        $anweisung->execute([$this->projektId]);

        return (array) $anweisung->fetch(\PDO::FETCH_ASSOC);
    }

    /** @return array<string,mixed> */
    private function angebot(): array
    {
        $anweisung = $this->pdo->prepare('SELECT * FROM offers WHERE id = ?');
        $anweisung->execute([$this->angebotId]);

        return (array) $anweisung->fetch(\PDO::FETCH_ASSOC);
    }

    /** @return array<string,mixed> */
    private function rechnung(string $id): array
    {
        $anweisung = $this->pdo->prepare('SELECT * FROM invoices WHERE id = ?');
        $anweisung->execute([$id]);

        return (array) $anweisung->fetch(\PDO::FETCH_ASSOC);
    }

    /** @return array<string,mixed> */
    private function aufgabe(string $id): array
    {
        $anweisung = $this->pdo->prepare('SELECT * FROM tasks WHERE id = ?');
        $anweisung->execute([$id]);

        return (array) $anweisung->fetch(\PDO::FETCH_ASSOC);
    }

    private function freigabeaufgabe(): string
    {
        $anweisung = $this->pdo->prepare(
            "SELECT id FROM tasks WHERE project_id = ? AND kind = 'freigabe' LIMIT 1"
        );
        $anweisung->execute([$this->projektId]);

        return (string) $anweisung->fetchColumn();
    }

    /** Ein Prüfprotokoll-Ereignis von Hand — für die Grenzfälle von Block 4. */
    private function ereignisAnlegen(string $organisationId, string $aktion, ?string $neuerWert): void
    {
        (new \Sartu\Data\AuditProtokoll($this->pdo))->schreiben(
            aktion: $aktion,
            objektart: 'project',
            objektId: $this->projektId,
            organisationId: $organisationId,
            neuerWert: $neuerWert,
        );
    }

    private function faelligkeitVerschieben(string $rechnungId, string $verschiebung): void
    {
        $neu = (new \DateTimeImmutable('now', new \DateTimeZone('Europe/Berlin')))
            ->modify($verschiebung)->format('Y-m-d');

        $anweisung = $this->pdo->prepare('UPDATE invoices SET due_date = ? WHERE id = ?');
        $anweisung->execute([$neu, $rechnungId]);
    }

    private function erinnerungVerschieben(string $rechnungId, int $tage): void
    {
        $anweisung = $this->pdo->prepare(
            'UPDATE invoices SET reminder_sent_at = DATE_SUB(UTC_TIMESTAMP(), INTERVAL ? DAY) WHERE id = ?'
        );
        $anweisung->execute([$tage, $rechnungId]);
    }

    /** @return array<string,mixed> */
    private function letztesEreignis(string $aktion): array
    {
        $anweisung = $this->pdo->prepare(
            'SELECT * FROM audit_events WHERE action = ? ORDER BY created_at DESC, id DESC LIMIT 1'
        );
        $anweisung->execute([$aktion]);

        $zeile = $anweisung->fetch(\PDO::FETCH_ASSOC);

        $this->assertIsArray($zeile, 'Kein Audit-Ereignis fuer ' . $aktion . '.');

        return $zeile;
    }

    private function router(): Router
    {
        return new Router(
            require SARTU_WURZEL . '/app/routes.php',
            new InstallationsSperre(new BetreiberdatenSpeicher($this->pdo), $this->arbeitsverzeichnis),
            new Wartungsmodus($this->arbeitsverzeichnis . '/ohne-wartung'),
        );
    }
}
