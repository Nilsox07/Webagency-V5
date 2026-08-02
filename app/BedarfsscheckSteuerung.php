<?php

declare(strict_types=1);

namespace Sartu;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Helpers\Http;
use Sartu\Services\Anfragebenachrichtigung;
use Sartu\Services\AnfrageService;
use Sartu\Services\Bedarfsscheck;
use Sartu\Services\BedarfsscheckSitzung;
use Sartu\Services\Empfehlung;
use Sartu\Services\Empfehlungstext;
use Sartu\Services\Herkunft;
use Sartu\Services\Preise;

/**
 * `/briefing` — der Bedarfsscheck, Website-Lastenheft §9.
 *
 * **Gebaut ist die Fassung ohne JavaScript**, wie §9.5a es verlangt: echte Seiten
 * `/briefing/1` bis `/briefing/5`, je Schritt ein `POST`, der Server antwortet mit dem
 * nächsten Schritt. Es gibt in dieser Strecke keine einzige Zeile Skript — nicht als
 * Bekenntnis, sondern weil der Bedarfsscheck der einzige Weg zu einem Angebot ist.
 *
 * ## Die Reihenfolge ist der Punkt
 *
 * | Seite | Was sie leistet |
 * |---|---|
 * | `/briefing` | Einstieg, §9.1 |
 * | `/briefing/1` … `/briefing/5` | die fünf Themen, §9.2 |
 * | `/briefing/ergebnis` | Empfehlung und Preis **vor** den Kontaktdaten, §9.3 |
 * | `/briefing/kontakt` | Kontaktdaten, §9.4 |
 * | `/briefing/danke` | §9.6 |
 *
 * Das Ergebnis steht vor den Kontaktdaten. Wer erst seine Adresse hergeben muss, um einen
 * Preis zu sehen, hat keinen Preis gesehen, sondern einen Handel gemacht.
 *
 * ## Wogegen der Sprungschutz steht
 *
 * Jede Seite ab Schritt 2 prüft `erreichbarerSchritt()`. Ohne diese Prüfung ließe sich
 * `/briefing/ergebnis` direkt aufrufen, und dort stünde eine Empfehlung, die auf leeren
 * Angaben beruht. Ein direkter Aufruf leitet deshalb auf den ersten offenen Schritt.
 */
final class BedarfsscheckSteuerung
{
    public function __construct(private readonly ?AnfrageService $dienst = null)
    {
    }

    // ------------------------------------------------------------------ §9.1 Einstieg

    /** @param array<string,string> $parameter */
    public function einstieg(array $parameter = []): Antwort
    {
        return Antwort::html(Ansicht::seite('oeffentlich', 'briefing-start', [
            'titel'        => 'Bedarf prüfen lassen — unverbindliche Empfehlung',
            'beschreibung' => 'Beantworten Sie wenige Fragen zu Ihrem Unternehmen und sehen Sie sofort '
                . 'eine vorläufige Empfehlung mit Festpreis. Unverbindlich, ohne Termin, in etwa drei Minuten.',
            // §9.5a: „Eine Kontaktalternative steht zusätzlich da, ersetzt den Bedarfsscheck
            // aber nicht."
            'kontaktweg'   => self::betreiberEmail(),
        ]));
    }

    /** @param array<string,string> $parameter */
    public function starten(array $parameter = []): Antwort
    {
        BedarfsscheckSitzung::starten();

        return Antwort::weiter('/briefing/1', 303);
    }

    // ------------------------------------------------------------------ §9.2 die fünf Themen

    /** @param array<string,string> $parameter */
    public function schritt(array $parameter = []): Antwort
    {
        $nummer = self::nummer($parameter);

        if ($nummer === null) {
            return Antwort::nichtGefunden();
        }

        BedarfsscheckSitzung::starten();

        // §9.5a „Zurück": ein normaler Link auf den vorigen Schritt, Angaben bleiben
        // erhalten. Vorwärts springen geht nicht — deshalb nur nach oben begrenzt.
        $erreichbar = BedarfsscheckSitzung::erreichbarerSchritt();

        if ($nummer > $erreichbar) {
            return Antwort::weiter('/briefing/' . $erreichbar, 303);
        }

        return $this->schrittSeite($nummer, BedarfsscheckSitzung::antworten(), []);
    }

    /** @param array<string,string> $parameter */
    public function schrittSpeichern(array $parameter = []): Antwort
    {
        $nummer = self::nummer($parameter);

        if ($nummer === null) {
            return Antwort::nichtGefunden();
        }

        BedarfsscheckSitzung::starten();

        $eingabe = self::eingabeZumSchritt($nummer);
        // Geprüft wird gegen die bisherigen Antworten PLUS die neuen: Eine bedingte Pflicht
        // („Adresse der bestehenden Website" nur bei „Ja") hängt an einem Feld desselben
        // Schritts, und das steht erst nach dem Zusammenführen vollständig da.
        $fehler = Bedarfsscheck::schrittPruefen($nummer, $eingabe);

        if ($fehler !== []) {
            return $this->schrittSeite($nummer, $eingabe + BedarfsscheckSitzung::antworten(), $fehler);
        }

        BedarfsscheckSitzung::merken($eingabe);

        $naechste = $nummer < Bedarfsscheck::SCHRITTE ? '/briefing/' . ($nummer + 1) : '/briefing/ergebnis';

        // 303 und nicht 302: Nach einem POST soll der Browser die nächste Seite mit GET
        // holen. Sonst wiederholt die Zurück-Taste das Absenden.
        return Antwort::weiter($naechste, 303);
    }

    // ------------------------------------------------------------------ §9.3 Ergebnis

    /** @param array<string,string> $parameter */
    public function ergebnis(array $parameter = []): Antwort
    {
        if (!BedarfsscheckSitzung::vollstaendig()) {
            return Antwort::weiter('/briefing/' . BedarfsscheckSitzung::erreichbarerSchritt(), 303);
        }

        $empfehlung = self::empfehlung(BedarfsscheckSitzung::antworten());

        return Antwort::html(Ansicht::seite('oeffentlich', 'briefing-ergebnis', [
            'titel'   => 'Ihre vorläufige Empfehlung',
            'noindex' => true,
            'text'    => Empfehlungstext::fuer(
                $empfehlung['paket'],
                self::liste(BedarfsscheckSitzung::antworten(), 'umfangssignale'),
            ),
            'preise'  => Preise::zeile($empfehlung['paket']),
            'preiszeile'  => Preise::preiszeile($empfehlung['paket']),
            'erstesJahr'  => Preise::erstesJahr($empfehlung['paket']),
        ]));
    }

    // ------------------------------------------------------------------ §9.4 Kontaktdaten

    /** @param array<string,string> $parameter */
    public function kontakt(array $parameter = []): Antwort
    {
        if (!BedarfsscheckSitzung::vollstaendig()) {
            return Antwort::weiter('/briefing/' . BedarfsscheckSitzung::erreichbarerSchritt(), 303);
        }

        return $this->kontaktSeite([], [], null);
    }

    /** @param array<string,string> $parameter */
    public function absenden(array $parameter = []): Antwort
    {
        if (!BedarfsscheckSitzung::vollstaendig()) {
            return Antwort::weiter('/briefing/' . BedarfsscheckSitzung::erreichbarerSchritt(), 303);
        }

        $eingabe = self::kontakteingabe() + BedarfsscheckSitzung::antworten() + [
            'submission_id'   => (string) BedarfsscheckSitzung::submissionId(),
            'form_started_at' => BedarfsscheckSitzung::begonnenAm(),
        ];

        $ergebnis = ($this->dienst ?? new AnfrageService())
            ->anlegen($eingabe, Herkunft::ausSitzung(), Http::gegenstelle() === '' ? null : Http::gegenstelle());

        if ($ergebnis->dankeSeite) {
            // §9.5b: „Zusätzlich: Benachrichtigungs-E-Mail an SARTU."
            //
            // Nur bei einem echten Datensatz — ein stillschweigend verworfener Versuch
            // sieht für den Absender aus wie ein Erfolg, soll aber kein Postfach füllen.
            // Ein Fehler beim Versand bleibt folgenlos: Der `lead` steht bereits.
            if ($ergebnis->wurdeGespeichert()) {
                (new Anfragebenachrichtigung())->senden(
                    (string) self::kontakteingabe()['company'],
                    (string) $ergebnis->paket,
                    (string) $ergebnis->ampel,
                );
            }

            // §9.5b: „Nie ein erneut absendbares Formular anzeigen." Der Zwischenstand geht
            // weg, damit ein Zurück nicht in ein ausgefülltes Formular führt.
            BedarfsscheckSitzung::verwerfen();
            $_SESSION['_briefing_danke'] = true;

            return Antwort::weiter('/briefing/danke', 303);
        }

        return $this->kontaktSeite(self::kontakteingabe(), $ergebnis->feldfehler, $ergebnis->meldung);
    }

    // ------------------------------------------------------------------ §9.6 Danke

    /** @param array<string,string> $parameter */
    public function danke(array $parameter = []): Antwort
    {
        if (($_SESSION['_briefing_danke'] ?? null) !== true) {
            return Antwort::weiter('/briefing', 303);
        }

        return Antwort::html(Ansicht::seite('oeffentlich', 'briefing-danke', [
            'titel'   => 'Danke — wir haben Ihre Angaben',
            'noindex' => true,
        ]));
    }

    // ------------------------------------------------------------------ intern

    /** @param array<string,mixed> $werte @param array<string,string> $fehler */
    private function schrittSeite(int $nummer, array $werte, array $fehler): Antwort
    {
        return Antwort::html(Ansicht::seite('oeffentlich', 'briefing-schritt', [
            'titel'   => Bedarfsscheck::thema($nummer)['titel'],
            'noindex' => true,
            'nummer'  => $nummer,
            'thema'   => Bedarfsscheck::thema($nummer),
            'werte'   => $werte,
            'fehler'  => $fehler,
        ]));
    }

    /** @param array<string,mixed> $werte @param array<string,string> $fehler */
    private function kontaktSeite(array $werte, array $fehler, ?string $meldung): Antwort
    {
        return Antwort::html(Ansicht::seite('oeffentlich', 'briefing-kontakt', [
            'titel'      => 'Ihre Kontaktdaten',
            'noindex'    => true,
            'werte'      => $werte,
            'fehler'     => $fehler,
            'meldung'    => $meldung,
            'kontaktweg' => self::betreiberEmail(),
            'quellen'    => Bedarfsscheck::HERKUNFTSANGABEN,
        ]));
    }

    /** @param array<string,mixed> $antworten @return array{paket:string,ampel:string,gruende:list<string>} */
    private static function empfehlung(array $antworten): array
    {
        return Empfehlung::bestimmen(
            self::liste($antworten, 'umfangssignale'),
            self::liste($antworten, 'sonderfunktionen'),
            bestehendeWebsiteUnklar: ($antworten['bestehende_website'] ?? null) === 'unsicher',
            zielgruppeUnklar: ($antworten['zielgruppe'] ?? null) === 'unklar',
            domainUnklar: ($antworten['domainstatus'] ?? null) === 'unsicher',
            festerTermin: ($antworten['fester_termin'] ?? null) === 'ja',
        );
    }

    /** Nur die Felder des angefragten Schritts — kein Formular schreibt in ein fremdes Thema. */
    private static function eingabeZumSchritt(int $nummer): array
    {
        $eingabe = [];

        foreach (Bedarfsscheck::thema($nummer)['felder'] as $feld) {
            $name = (string) $feld['name'];
            $wert = $_POST[$name] ?? null;

            if (($feld['art'] ?? '') === 'checkbox') {
                $eingabe[$name] = is_array($wert)
                    ? array_values(array_filter($wert, static fn ($w) => is_string($w) && $w !== ''))
                    : [];
                continue;
            }

            $eingabe[$name] = is_string($wert) ? trim($wert) : '';
        }

        return $eingabe;
    }

    /** @return array<string,mixed> */
    private static function kontakteingabe(): array
    {
        $felder = [
            'first_name', 'last_name', 'company', 'email', 'phone', 'preferred_contact',
            'b2b_confirmed', 'privacy_confirmed', 'self_reported_source',
            'self_reported_source_text', 'hp_website',
        ];

        $eingabe = [];

        foreach ($felder as $feld) {
            $wert = $_POST[$feld] ?? null;
            $eingabe[$feld] = is_string($wert) ? trim($wert) : '';
        }

        return $eingabe;
    }

    /** @return list<string> */
    private static function liste(array $werte, string $feld): array
    {
        $wert = $werte[$feld] ?? [];

        if (!is_array($wert)) {
            return [];
        }

        return array_values(array_filter($wert, static fn ($w) => is_string($w) && $w !== ''));
    }

    /** @param array<string,string> $parameter */
    private static function nummer(array $parameter): ?int
    {
        $roh = $parameter['nummer'] ?? '';

        if (preg_match('/^[1-9][0-9]*$/', $roh) !== 1) {
            return null;
        }

        $nummer = (int) $roh;

        return $nummer >= 1 && $nummer <= Bedarfsscheck::SCHRITTE ? $nummer : null;
    }

    /**
     * Die Kontaktalternative aus §9.5b — „oder schreiben Sie uns an {E-Mail}".
     *
     * Sie kommt aus den Betreiberdaten, nicht aus einer Konstante. Steht dort nichts, steht
     * auf der Seite auch nichts: Eine erfundene Adresse wäre schlimmer als keine.
     */
    private static function betreiberEmail(): ?string
    {
        try {
            $daten = (new BetreiberdatenSpeicher())->lesen();
        } catch (\Throwable) {
            return null;
        }

        $email = $daten['email'] ?? null;

        return is_string($email) && trim($email) !== '' ? trim($email) : null;
    }
}
