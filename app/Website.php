<?php

declare(strict_types=1);

namespace Sartu;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\Uuid;
use Sartu\Helpers\Http;
use Sartu\Services\Auftragslage;
use Sartu\Services\Branchenseiten;
use Sartu\Services\Firmenseitentexte;
use Sartu\Services\Herkunft;
use Sartu\Services\Kontaktanfrage;
use Sartu\Services\Lexikon;
use Sartu\Services\Ratgeber;
use Sartu\Services\Leistungsseiten;
use Sartu\Services\Startseitentexte;
use Sartu\Services\Unterseitentexte;
use Sartu\Services\Websitetexte;

/**
 * Die öffentliche SARTU-Website — Website-Lastenheft §5 ff.
 *
 * ## Kein Sitzungsbezug, deshalb cachebar
 *
 * §1: „Öffentliche Seiten hängen nicht an einer Sitzung und dürfen als statische Antwort
 * ausgeliefert werden." Diese Klasse liest deshalb **keine** Sitzung. Sie liest genau eine
 * Zeile aus der Datenbank — die Betreiberdaten — und daraus genau zwei Werte: die
 * Auftragslage (§5a) und `kleinunternehmer` (§19 UStG).
 *
 * ## Warum die zwei Werte trotzdem aus der Datenbank kommen
 *
 * Beide sind Aussagen über den Betrieb. §5a: „Der Wert wird im internen Bereich gepflegt und
 * nie im Quelltext erfunden." Und die Umsatzsteuerzeile ist ein Pflichthinweis, dessen
 * richtige Fassung davon abhängt, ob der Betreiber Kleinunternehmer ist.
 *
 * **Fehlt die Zeile, gilt: nichts anzeigen und mit Umsatzsteuer rechnen.** Ein fehlender
 * Wert ist keine Auftragslage, und die Regelbesteuerung ist der Fall, in dem der
 * Pflichthinweis stehen muss.
 */
final class Website
{
    public function __construct(private readonly ?BetreiberdatenSpeicher $betrieb = null)
    {
    }

    /** @param array<string,string> $parameter */
    public function start(array $parameter = []): Antwort
    {
        return $this->seite('website-start', [
            'titel'        => Startseitentexte::TITEL,
            'beschreibung' => Startseitentexte::BESCHREIBUNG,
            'pfad'         => '/',
            'schema'       => Strukturdaten::organisationUndWebsite(),
        ]);
    }

    /** @param array<string,string> $parameter */
    public function leistungen(array $parameter = []): Antwort
    {
        return $this->seite('website-leistungen', [
            'titel'        => Unterseitentexte::LEISTUNGEN_TITEL,
            'beschreibung' => Unterseitentexte::LEISTUNGEN_BESCHREIBUNG,
            'pfad'         => '/leistungen',
            'brotkrumen'   => [['/leistungen', 'Leistungen']],
            'schema'       => Strukturdaten::verbinden(
                Strukturdaten::dienstleistung(
                    'Firmenwebsite zum Festpreis',
                    Unterseitentexte::LEISTUNGEN_BESCHREIBUNG,
                    '/leistungen',
                ),
                Strukturdaten::brotkrumen([['/leistungen', 'Leistungen']]),
            ),
        ]);
    }

    /** @param array<string,string> $parameter */
    public function preise(array $parameter = []): Antwort
    {
        return $this->seite('website-preise', [
            'titel'        => Unterseitentexte::PREISE_TITEL,
            'beschreibung' => Unterseitentexte::PREISE_BESCHREIBUNG,
            'pfad'         => '/preise',
            'brotkrumen'   => [['/preise', 'Preise']],
            'schema'       => Strukturdaten::verbinden(
                Strukturdaten::dienstleistung(
                    'Firmenwebsite zum Festpreis',
                    Unterseitentexte::PREISE_BESCHREIBUNG,
                    '/preise',
                ),
                Strukturdaten::brotkrumen([['/preise', 'Preise']]),
            ),
        ]);
    }

    /** @param array<string,string> $parameter */
    public function ablauf(array $parameter = []): Antwort
    {
        return $this->seite('website-ablauf', [
            'titel'        => Unterseitentexte::ABLAUF_TITEL,
            'beschreibung' => Unterseitentexte::ABLAUF_BESCHREIBUNG,
            'pfad'         => '/ablauf',
            'brotkrumen'   => [['/ablauf', 'Ablauf']],
            'schema'       => Strukturdaten::brotkrumen([['/ablauf', 'Ablauf']]),
        ]);
    }

    /**
     * Die fünf Leistungsseiten — §10, ein Template und fünf Datensätze.
     *
     * **Fünf eigene Routen und fünf Einzeiler statt einer Route mit Platzhalter.** Der
     * Router setzt Platzhalter nur für einen **ganzen** Pfadabschnitt ein; `/leistung-{x}`
     * ist ein Abschnitt mit Präfix. Ihn dafür zu erweitern hieße, die Mustererkennung
     * anzufassen, an der `TenantIsolationTest` die vollständige Routenliste misst — für fünf
     * feste Adressen, die §16 ohnehin einzeln aufzählt.
     *
     * @param array<string,string> $parameter
     */
    public function webdesign(array $parameter = []): Antwort { return $this->leistung('webdesign'); }

    /** @param array<string,string> $parameter */
    public function texte(array $parameter = []): Antwort { return $this->leistung('texte'); }

    /** @param array<string,string> $parameter */
    public function seoLokal(array $parameter = []): Antwort { return $this->leistung('seo-lokal'); }

    /** @param array<string,string> $parameter */
    public function wartung(array $parameter = []): Antwort { return $this->leistung('wartung'); }

    /** @param array<string,string> $parameter */
    public function portal(array $parameter = []): Antwort { return $this->leistung('portal'); }

    private function leistung(string $schluessel): Antwort
    {
        $seite = Leistungsseiten::finden($schluessel);

        if ($seite === null) {
            return Antwort::nichtGefunden();
        }

        $pfad = '/leistung-' . $schluessel;
        $krumen = [['/leistungen', 'Leistungen'], [$pfad, (string) $seite['h1']]];

        return $this->seite('website-leistung', [
            'titel'        => (string) $seite['titel'],
            'beschreibung' => (string) $seite['beschreibung'],
            'pfad'         => $pfad,
            'brotkrumen'   => $krumen,
            'seite'        => $seite,
            'schluessel'   => $schluessel,
            'schema'       => Strukturdaten::verbinden(
                Strukturdaten::dienstleistung(
                    (string) $seite['h1'],
                    (string) $seite['beschreibung'],
                    $pfad,
                ),
                Strukturdaten::brotkrumen($krumen),
            ),
        ]);
    }

    /** @param array<string,string> $parameter */
    public function ueberUns(array $parameter = []): Antwort
    {
        $krumen = [['/ueber-uns', 'Über uns']];

        return $this->seite('website-ueber-uns', [
            'titel'        => Firmenseitentexte::UEBER_TITEL,
            'beschreibung' => Firmenseitentexte::UEBER_BESCHREIBUNG,
            'pfad'         => '/ueber-uns',
            'brotkrumen'   => $krumen,
            'schema'       => Strukturdaten::brotkrumen($krumen),
        ]);
    }

    /**
     * `/kontakt` — die Seite mit dem Rückfrageformular.
     *
     * @param array<string,string> $parameter
     * @param array<string,mixed> $werte      bei einem Fehler die Eingaben, damit niemand neu tippt
     * @param array<string,string> $fehler
     */
    public function kontakt(array $parameter = [], array $werte = [], array $fehler = [], ?string $meldung = null): Antwort
    {
        $krumen = [['/kontakt', 'Kontakt']];

        return $this->seite('website-kontakt', [
            'titel'        => Firmenseitentexte::KONTAKT_TITEL,
            'beschreibung' => Firmenseitentexte::KONTAKT_BESCHREIBUNG,
            'pfad'         => '/kontakt',
            'brotkrumen'   => $krumen,
            'schema'       => Strukturdaten::brotkrumen($krumen),
            'werte'        => $werte,
            'fehler'       => $fehler,
            'meldung'      => $meldung,
            // Zeitregel und Doppeleinreichung aus §4b — dieselben Felder wie im Bedarfsscheck.
            'zeitstempel'  => (string) time(),
            'einreichung'  => $werte['submission_id'] ?? Uuid::v4(),
        ]);
    }

    /**
     * Nimmt die Rückfrage an — §11.
     *
     * **Alle stillen Ausgänge führen auf dieselbe Bestätigungsseite.** Honigtopf, Zeitregel
     * und Doppeleinreichung sehen für den Absender aus wie ein Erfolg (§4b.2).
     *
     * @param array<string,string> $parameter
     */
    public function kontaktSenden(array $parameter = []): Antwort
    {
        $ergebnis = (new Kontaktanfrage())->anlegen(
            $_POST,
            Herkunft::ausSitzung(),
            Http::gegenstelle() === '' ? null : Http::gegenstelle(),
        );

        if ($ergebnis->dankeSeite) {
            return $this->seite('website-danke', [
                'titel'        => 'Danke für Ihre Nachricht | SARTU',
                'beschreibung' => null,
                'pfad'         => '/kontakt/danke',
                'noindex'      => true,
                'satz'         => Kontaktanfrage::BESTAETIGUNG,
            ]);
        }

        return $this->kontakt($parameter, $_POST, $ergebnis->feldfehler, $ergebnis->meldung);
    }

    /**
     * Die 404-Seite — §14.
     *
     * Sie wird vom Router aufgerufen, nicht über eine Route erreicht. Eine Adresse, unter der
     * die Fehlerseite steht, wäre eine Seite, die es gibt.
     */
    public function nichtGefunden(): Antwort
    {
        return Antwort::html($this->rumpf('website-404', [
            'titel'        => 'Diese Seite gibt es nicht | SARTU',
            'beschreibung' => null,
            'pfad'         => '/404',
            'noindex'      => true,
        ]), 404);
    }

    // ---------------------------------------------------------------- Branchenseiten (§10a)

    /** @param array<string,string> $parameter */
    public function shk(array $parameter = []): Antwort { return $this->branche('sanitaer-heizung-klima'); }

    /** @param array<string,string> $parameter */
    public function elektro(array $parameter = []): Antwort { return $this->branche('elektrotechnik'); }

    /** @param array<string,string> $parameter */
    public function dachdecker(array $parameter = []): Antwort { return $this->branche('dachdecker'); }

    private function branche(string $schluessel): Antwort
    {
        $seite = Branchenseiten::finden($schluessel);

        if ($seite === null) {
            return $this->nichtGefunden();
        }

        $pfad = '/website-' . $schluessel;
        $krumen = [[$pfad, (string) $seite['branche']]];

        return $this->seite('website-branche', [
            'titel'        => (string) $seite['titel'],
            'beschreibung' => (string) $seite['beschreibung'],
            'pfad'         => $pfad,
            'brotkrumen'   => $krumen,
            'seite'        => $seite,
            'schluessel'   => $schluessel,
            'schema'       => Strukturdaten::verbinden(
                Strukturdaten::dienstleistung(
                    (string) $seite['h1'],
                    (string) $seite['beschreibung'],
                    $pfad,
                ),
                Strukturdaten::brotkrumen($krumen),
            ),
        ]);
    }

    // ---------------------------------------------------------------- Ratgeber (§11a, §12)

    /** @param array<string,string> $parameter */
    public function ratgeber(array $parameter = []): Antwort
    {
        $krumen = [['/ratgeber', 'Ratgeber']];

        return $this->seite('website-ratgeber-hub', [
            'titel'        => Ratgeber::HUB_TITEL,
            'beschreibung' => Ratgeber::HUB_BESCHREIBUNG,
            'pfad'         => '/ratgeber',
            'brotkrumen'   => $krumen,
            'schema'       => Strukturdaten::brotkrumen($krumen),
        ]);
    }

    /** @param array<string,string> $parameter */
    public function ratgeberArtikel(array $parameter = []): Antwort
    {
        $schluessel = (string) ($parameter['schluessel'] ?? '');
        $artikel = Ratgeber::finden($schluessel);

        if ($artikel === null) {
            return $this->nichtGefunden();
        }

        $pfad = '/ratgeber/' . $schluessel;
        $krumen = [['/ratgeber', 'Ratgeber'], [$pfad, (string) $artikel['h1']]];

        return $this->seite('website-ratgeber', [
            'titel'        => (string) $artikel['titel'],
            'beschreibung' => (string) $artikel['beschreibung'],
            'pfad'         => $pfad,
            'brotkrumen'   => $krumen,
            'artikel'      => $artikel,
            'schema'       => Strukturdaten::verbinden(
                Strukturdaten::artikel(
                    (string) $artikel['h1'],
                    (string) $artikel['beschreibung'],
                    $pfad,
                    Ratgeber::STAND,
                ),
                Strukturdaten::brotkrumen($krumen),
            ),
        ]);
    }

    // ---------------------------------------------------------------- Lexikon (§13)

    /** @param array<string,string> $parameter */
    public function lexikon(array $parameter = []): Antwort
    {
        $begriffe = [];

        foreach (Lexikon::alle() as $schluessel => $eintrag) {
            $begriffe[] = [
                'schluessel' => $schluessel,
                'begriff'    => (string) $eintrag['begriff'],
                'kurz'       => (string) $eintrag['kurz'],
            ];
        }

        // §13: alphabetisch — nach dem Begriff, nicht nach dem Schlüssel. `local-seo` stünde
        // sonst vor `relaunch`, obwohl „Local SEO" davor gehört und „GEO" ganz nach vorn.
        usort($begriffe, static fn (array $a, array $b) => strcoll($a['begriff'], $b['begriff']));

        $krumen = [['/lexikon', 'Lexikon']];

        return $this->seite('website-lexikon-hub', [
            'titel'        => Lexikon::HUB_TITEL,
            'beschreibung' => Lexikon::HUB_BESCHREIBUNG,
            'pfad'         => '/lexikon',
            'brotkrumen'   => $krumen,
            'begriffe'     => $begriffe,
            'schema'       => Strukturdaten::brotkrumen($krumen),
        ]);
    }

    /** @param array<string,string> $parameter */
    public function lexikonBegriff(array $parameter = []): Antwort
    {
        $schluessel = (string) ($parameter['schluessel'] ?? '');
        $eintrag = Lexikon::finden($schluessel);

        if ($eintrag === null) {
            return $this->nichtGefunden();
        }

        $pfad = '/lexikon/' . $schluessel;
        $krumen = [['/lexikon', 'Lexikon'], [$pfad, (string) $eintrag['begriff']]];

        return $this->seite('website-lexikon', [
            'titel'        => (string) $eintrag['begriff'] . ' — einfach erklärt | SARTU',
            'beschreibung' => (string) $eintrag['kurz'],
            'pfad'         => $pfad,
            'brotkrumen'   => $krumen,
            'eintrag'      => $eintrag,
            'verwandte'    => self::verwandteAdressen($eintrag['verwandt']),
            'schema'       => Strukturdaten::verbinden(
                Strukturdaten::begriff((string) $eintrag['begriff'], (string) $eintrag['kurz'], $pfad),
                Strukturdaten::brotkrumen($krumen),
            ),
        ]);
    }

    /**
     * Verwandte Begriffe in Adressen auflösen.
     *
     * Ein Begriff, den es nicht gibt, fällt heraus statt ins Leere zu zeigen (§0.3b).
     *
     * @param list<string> $namen
     * @return array<string,string>
     */
    private static function verwandteAdressen(array $namen): array
    {
        $adressen = [];

        foreach (Lexikon::alle() as $schluessel => $eintrag) {
            $adressen[(string) $eintrag['begriff']] = '/lexikon/' . $schluessel;
        }

        $treffer = [];

        foreach ($namen as $name) {
            if (isset($adressen[$name])) {
                $treffer[$name] = $adressen[$name];
            }
        }

        return $treffer;
    }

    // ------------------------------------------------------------------ intern

    /**
     * Baut eine öffentliche Seite mit allem, was §17 von jeder Seite verlangt.
     *
     * @param array<string,mixed> $werte
     */
    private function seite(string $ansicht, array $werte): Antwort
    {
        return Antwort::html($this->rumpf($ansicht, $werte));
    }

    /**
     * Der fertige HTML-Rumpf — getrennt von `seite()`, weil die 404-Seite denselben Rahmen
     * braucht, aber einen anderen Status.
     *
     * @param array<string,mixed> $werte
     */
    private function rumpf(string $ansicht, array $werte): string
    {
        $daten = $this->betriebsdaten();

        return Ansicht::seite('website', $ansicht, $werte + [
            'brotkrumen'       => [],
            'noindex'          => false,
            'schema'           => null,
            'auftragslage'     => Auftragslage::anzeige($daten['auftragslage']),
            'preishinweis'     => Websitetexte::preishinweis($daten['kleinunternehmer']),
            'kleinunternehmer' => $daten['kleinunternehmer'],
            'werte'            => [],
            'fehler'           => [],
            'meldung'          => null,
        ]);
    }

    /** @return array{auftragslage:?string,kleinunternehmer:bool} */
    private function betriebsdaten(): array
    {
        try {
            $zeile = ($this->betrieb ?? new BetreiberdatenSpeicher())->lesen();
        } catch (\Throwable) {
            // Ohne Datenbank steht die Seite trotzdem. Sie zeigt dann keine Auftragslage —
            // das ist der Zustand „nicht gesetzt", und der ist in §5a vorgesehen.
            $zeile = null;
        }

        $lage = $zeile['auftragslage'] ?? null;

        return [
            'auftragslage'     => is_string($lage) && $lage !== '' ? $lage : null,
            'kleinunternehmer' => (int) ($zeile['kleinunternehmer'] ?? 0) === 1,
        ];
    }
}
