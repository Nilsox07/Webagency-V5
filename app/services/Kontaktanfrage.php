<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Helpers\Validate;

/**
 * Das Rückfrageformular auf `/kontakt` — Portal-Lastenheft §4b.6, Website-Lastenheft §11.
 *
 * ## Die Regel, an der sich alles hier entscheidet
 *
 * §4b.6, wörtlich:
 *
 * > „Das allgemeine Kontaktformular ist **nicht** der Bedarfsscheck. Es versendet
 * > ausschließlich eine E-Mail an SARTU und erzeugt **keinen** Datensatz. Honigtopf,
 * > Zeitregel und Rate-Limit gelten dort gleichermaßen."
 *
 * **Kein Datensatz.** Nicht in `leads`, nicht in `support_messages`, nirgends. Die vorige
 * Fassung legte die Rückfrage in `leads` ab — sie hatte §4b.6 nicht gefunden und aus dem
 * Fehlen einer Tabelle geschlossen, dass eine gesucht werden müsse. Gesucht war das
 * Gegenteil.
 *
 * **Das ist keine Kleinigkeit.** Löschfrist, Rechtsgrundlage und Verarbeitungsverzeichnis
 * von `leads` sind für den Bedarfsscheck geschrieben. Eine Rückfrage dort abzulegen heisst,
 * personenbezogene Daten unter einer Zweckbestimmung zu speichern, die sie nicht deckt.
 *
 * ## Drei Folgen, die aus „keine Speicherung" zwingend entstehen
 *
 * ### 1. Die Mail trägt den vollständigen Inhalt
 *
 * Beim Bedarfsscheck ist die Benachrichtigung eine **Kurzmeldung ohne Datenauszug** (§10) —
 * sie darf knapp sein, weil der Datensatz alles trägt und im Adminbereich hinter der
 * Zugriffsprüfung liegt.
 *
 * Hier gibt es keinen Datensatz. Die Mail **ist** die Rückfrage. Stünde nur „Es ist eine
 * Rückfrage eingegangen" darin, wäre die Rückfrage weg.
 *
 * ### 2. Eine gescheiterte Mail wird gemeldet, nicht verschwiegen
 *
 * Überall sonst gilt: Eine gescheiterte Mail nimmt keinen Vorgang zurück, weil der Vorgang
 * in der Datenbank steht. Hier steht er nirgends. Geht die Mail nicht raus, ist die
 * Rückfrage verloren — und dann eine Bestätigungsseite zu zeigen, wäre eine Lüge.
 *
 * Deshalb ist das der **einzige** Fall im Projekt, in dem ein Mailfehler den Absender
 * erreicht. Er bekommt den Hinweis samt Ausweichweg.
 *
 * ### 3. Die Doppelklicksperre liegt in der Sitzung, nicht in der Datenbank
 *
 * `leads.submission_id` ist eindeutig — daran erkennt der Bedarfsscheck die zweite
 * Einreichung. Ohne Zeile gibt es nichts zu vergleichen. Die verbrauchten Kennungen liegen
 * deshalb in der Sitzung; eine Tabelle dafür zu erfinden verstiesse gegen §4b.6 genauso wie
 * die alte Fassung.
 *
 * ## Was **nicht** abgefragt wird
 *
 * Die **B2B-Bestätigung ist raus**. Sie stand im Formular allein deshalb, weil
 * `chk_leads_bestaetigungen` beide Häkchen verlangte. Ohne `leads`-Zeile gibt es die
 * Prüfbedingung nicht — und Website-Lastenheft §11 zählt sieben Felder mit **einer**
 * Bestätigung auf. Ein Häkchen, das keinen Zweck mehr hat, ist eine Hürde ohne Grund.
 */
final class Kontaktanfrage
{
    /** §11 — die vier Auswahlmöglichkeiten unter „Anliegen". */
    public const ANLIEGEN = [
        'websiteprojekt' => 'Websiteprojekt',
        'angebot'        => 'Bestehendes Angebot',
        'domain'         => 'Domain und Launch',
        'rueckfrage'     => 'Allgemeine Rückfrage',
    ];

    /** §11: „Nachricht (Pflicht, min. 20 Zeichen)". */
    public const NACHRICHT_MINDESTLAENGE = 20;

    /** §11, Wortlaut gebunden. */
    public const BESTAETIGUNG = 'Danke — Ihre Nachricht ist angekommen. Wir antworten '
        . 'schriftlich, in der Regel innerhalb eines Werktags.';

    /** Der Sitzungsschlüssel für die verbrauchten Einreichungskennungen. */
    private const VERBRAUCHT = '_kontakt_verbraucht';

    public function __construct(
        private readonly ?Ratenbegrenzung $begrenzung = null,
        private readonly ?Projektmail $mail = null,
    ) {
    }

    /**
     * Nimmt eine Rückfrage an und verschickt sie.
     *
     * @param array<string,mixed> $eingabe
     */
    public function senden(array $eingabe, ?string $ip = null): AnfrageErgebnis
    {
        if (Formularschutz::zuGross($eingabe)) {
            return AnfrageErgebnis::abgewiesen(['Ihre Nachricht ist zu lang. Bitte kürzen Sie sie.']);
        }

        // §4b.6: Rate-Limit gilt „gleichermaßen" — dieselbe Zahl je IP und Stunde wie beim
        // Bedarfsscheck.
        $schluessel = 'kontakt-ip:' . (string) $ip;

        if (!$this->begrenzung()->erlaubt($schluessel, AnfrageService::VERSUCHE_JE_IP, Formularschutz::FENSTER_SEKUNDEN)) {
            return AnfrageErgebnis::begrenzt();
        }

        // §4b.6: Honigtopf und Zeitregel. Beide still — der Absender sieht die
        // Bestätigungsseite, nicht den Grund. Wer erfährt, dass er aufgefallen ist, macht es
        // beim nächsten Mal besser.
        if (Formularschutz::honigtopfGefuellt($eingabe)) {
            return AnfrageErgebnis::stillVerworfen();
        }

        if (!Formularschutz::zeitregelErfuellt(self::text($eingabe, 'form_started_at'))) {
            return AnfrageErgebnis::stillVerworfen();
        }

        $einreichung = self::text($eingabe, 'submission_id');

        if (!Formularschutz::istUuid($einreichung) || self::schonVerschickt($einreichung)) {
            return AnfrageErgebnis::stillVerworfen();
        }

        $fehler = $this->pflichtfelderPruefen($eingabe);

        if ($fehler !== []) {
            return AnfrageErgebnis::abgewiesenAmFeld($fehler);
        }

        $this->begrenzung()->vermerken($schluessel, Formularschutz::FENSTER_SEKUNDEN);

        if (!$this->hinausschicken($eingabe)) {
            // Kein Datensatz, keine Mail — die Rückfrage ist weg. Das sagen wir.
            return AnfrageErgebnis::abgewiesen([
                'Ihre Nachricht konnte gerade nicht zugestellt werden. Bitte versuchen Sie es '
                . 'in einigen Minuten noch einmal oder schreiben Sie uns direkt.',
            ]);
        }

        self::alsVerschicktVermerken($einreichung);

        // Kein Datensatz — also auch keine Kennung, kein Paket, keine Ampel.
        return AnfrageErgebnis::verschickt();
    }

    /**
     * Ist ein Empfänger hinterlegt?
     *
     * **Ohne ihn kann das Formular nichts.** §4b.6 nimmt ihm den Datensatz; bleibt die
     * Mail aus, ist die Rückfrage weg. Ein Formular, das sichtbar dasteht und jede Eingabe
     * verliert, ist schlimmer als keins — §0.3b verbietet genau solche toten Funktionen.
     *
     * Die Seite zeigt deshalb den Ausweichweg statt des Formulars, solange
     * `operator_settings.benachrichtigung_email` leer ist und auch `ADMIN_NOTIFY_EMAIL`
     * nichts hergibt.
     *
     * **Das ändert die Tragweite einer früheren Entscheidung.** Am 02.08.2026 stand in
     * `OFFENE_ENTSCHEIDUNGEN.md`: Ist das Feld leer, unterbleibt „**nur diese eine**
     * Benachrichtigung". Mit §4b.6 stimmt das nicht mehr — dann steht auch `/kontakt` still.
     */
    public function empfaengerVorhanden(): bool
    {
        return (new Anfragebenachrichtigung(null, null))->empfaenger() !== null;
    }

    // ------------------------------------------------------------------ intern

    /**
     * Die Mail an SARTU — §4b.6: „versendet ausschließlich eine E-Mail an SARTU".
     *
     * **Sie trägt alles.** Anders als beim Bedarfsscheck gibt es keinen Datensatz, den man
     * im Adminbereich nachlesen könnte. Was hier fehlt, ist verloren.
     */
    private function hinausschicken(array $eingabe): bool
    {
        // Der Betreff traegt NUR die feste Beschriftung aus `ANLIEGEN`, nie den Rohwert.
        // `pflichtfelderPruefen()` hat den Schluessel bereits gegen die Liste geprueft — ein
        // Rueckfall auf die Eingabe koennte hier also gar nicht auslaufen. Er stand trotzdem
        // da, und ein Zeilenumbruch in einem Mailbetreff ist der Anfang jeder
        // Kopfzeilen-Einschleusung. Was nicht ausloesen kann, muss auch nicht dastehen.
        $anliegen = self::ANLIEGEN[self::text($eingabe, 'anliegen')];
        $telefon = self::textOderNull($eingabe, 'phone');

        return ($this->mail ?? new Projektmail())->anBetreuer(
            // Ohne Projekt: `Projektmail` lässt die Zeile „Diese Nachricht bezieht sich auf
            // Ihr Projekt …" dann weg. Eine Rückfrage gehört zu keinem Projekt.
            ['title' => ''],
            'Rückfrage über die Website: ' . $anliegen,
            'Über das Kontaktformular ist eine Rückfrage eingegangen.' . "\n\n"
            . 'Name: ' . self::text($eingabe, 'name') . "\n"
            . 'Unternehmen: ' . self::text($eingabe, 'company') . "\n"
            . 'E-Mail: ' . mb_strtolower(self::text($eingabe, 'email')) . "\n"
            . 'Telefon: ' . ($telefon ?? 'nicht angegeben') . "\n"
            . 'Anliegen: ' . $anliegen . "\n\n"
            . 'Nachricht:' . "\n"
            . self::text($eingabe, 'nachricht') . "\n\n"
            . 'Diese Rückfrage ist nirgends gespeichert (§4b.6). Antworten Sie direkt auf '
            . "diese Mail.\n",
        );
    }

    /**
     * @return array<string,string> je Feldname eine Meldung
     *
     * §11 zählt sieben Felder auf. Pflicht sind Name, Unternehmen, E-Mail, Anliegen,
     * Nachricht und die Datenschutz-Bestätigung. Telefon ist ausdrücklich optional, und eine
     * Pflicht-Telefonnummer ist dort ausdrücklich verboten.
     */
    private function pflichtfelderPruefen(array $eingabe): array
    {
        $fehler = [];

        if (!Validate::gefuellt(self::text($eingabe, 'name'))) {
            $fehler['name'] = 'Bitte geben Sie Ihren Namen an.';
        }

        if (!Validate::gefuellt(self::text($eingabe, 'company'))) {
            $fehler['company'] = 'Bitte geben Sie Ihr Unternehmen an.';
        }

        if (!Validate::email(self::text($eingabe, 'email'))) {
            $fehler['email'] = 'Bitte geben Sie eine gültige E-Mail-Adresse an, z. B. name@firma.de';
        }

        if (!isset(self::ANLIEGEN[self::text($eingabe, 'anliegen')])) {
            $fehler['anliegen'] = 'Bitte wählen Sie, worum es geht.';
        }

        if (mb_strlen(self::text($eingabe, 'nachricht')) < self::NACHRICHT_MINDESTLAENGE) {
            // §11, Wortlaut gebunden.
            $fehler['nachricht'] = 'Bitte beschreiben Sie Ihr Anliegen in ein bis zwei Sätzen.';
        }

        if (!self::wahr($eingabe, 'privacy_confirmed')) {
            $fehler['privacy_confirmed'] = 'Bitte bestätigen Sie die Datenschutzhinweise.';
        }

        return $fehler;
    }

    /**
     * Die Doppelklicksperre — in der Sitzung, weil es keine Zeile gibt.
     *
     * Sie hält nur, solange die Sitzung hält. Das genügt: Doppelklick, Neuladen und die
     * Zurück-Taste passieren innerhalb einer Sitzung. Gegen jemanden, der eine Stunde später
     * bewusst dasselbe Formular noch einmal schickt, hilft das Rate-Limit.
     */
    private static function schonVerschickt(string $einreichung): bool
    {
        $verbraucht = $_SESSION[self::VERBRAUCHT] ?? [];

        return is_array($verbraucht) && in_array($einreichung, $verbraucht, true);
    }

    private static function alsVerschicktVermerken(string $einreichung): void
    {
        $verbraucht = $_SESSION[self::VERBRAUCHT] ?? [];
        $verbraucht = is_array($verbraucht) ? $verbraucht : [];
        $verbraucht[] = $einreichung;

        // Die Liste bleibt kurz. Eine Sitzung, die zehn Rückfragen abschickt, hat ein
        // anderes Problem als die Länge dieses Feldes.
        $_SESSION[self::VERBRAUCHT] = array_slice($verbraucht, -10);
    }

    private static function text(array $eingabe, string $feld): string
    {
        $wert = $eingabe[$feld] ?? null;

        return is_string($wert) ? trim($wert) : '';
    }

    private static function textOderNull(array $eingabe, string $feld): ?string
    {
        $wert = self::text($eingabe, $feld);

        return $wert === '' ? null : $wert;
    }

    private static function wahr(array $eingabe, string $feld): bool
    {
        return in_array($eingabe[$feld] ?? null, ['1', 'ja', 'on', true], true);
    }

    private function begrenzung(): Ratenbegrenzung
    {
        return $this->begrenzung ?? new Ratenbegrenzung();
    }
}
