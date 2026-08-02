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

    // ---------------------------------------------------------------- Hilfsmittel

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
