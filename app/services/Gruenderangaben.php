<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\BetreiberdatenSpeicher;

/**
 * Sektion 6 „Wer dahintersteckt" — `10_WEBSITE_SARTU.md` und `SARTU_ENTSCHEIDUNGEN_OFFEN.md`
 * §4c (Rang 1, entschieden 10.08.2026).
 *
 * ## Die Sperre liegt jetzt an den Daten, nicht am Code
 *
 * Bis zum 13.08.2026 war die Sektion **gar nicht gebaut**. Die Begründung stand im Kopf von
 * `Startseitentexte`: Name und Foto stehen in §5 auf `offen`, und ein leerer Rahmen an einer
 * Vertrauensstelle ist unzulässig.
 *
 * **Der zweite Teil davon gilt weiter, der erste nicht mehr.** §4c: „Feld leer → nichts wird
 * ausgeliefert. Feld gefüllt → alles läuft." Der Rahmen bleibt also leer verboten — nur
 * entscheidet das jetzt der Inhalt der Betreiberdaten und nicht mehr eine fehlende Datei.
 *
 * ## Alle drei Angaben, nicht eine davon
 *
 * §4c nennt „Name, Text und Bild" zusammen. `sichtbar()` verlangt deshalb alle drei. Zwei von
 * dreien wären genau der halbe Rahmen, den §5 verbietet: ein Absatz ohne Gesicht oder ein
 * Gesicht ohne Grund.
 *
 * ## Was hier **nicht** entschieden wird
 *
 * Ob der volle Klarname oder nur Vorname und Rolle im Feld steht, ist §5.1 — eine persönliche
 * Entscheidung des Betreibers. Das Feld nimmt beides; diese Klasse liest es und prüft es
 * nicht auf seine Form.
 */
final class Gruenderangaben
{
    /**
     * §5 Sektion 6, H2 — *Aufgabe:* die Person benennen und die Verantwortung über den Launch
     * hinaus zusagen. *Umfang:* zwei Sätze, zusammen höchstens neun Wörter. Hier sind es acht.
     *
     * Der Satz steht im Texter-Skill unter „Kalibrierung" als geprüfte Fassung: `eine Person`
     * ist die nachprüfbare Angabe, `Dieselbe antwortet danach` die Zusage nach dem Launch.
     * **„Unser Team" kommt nicht vor** — `06_RECHT.md` verbietet es, solange eine Einzelperson
     * arbeitet.
     */
    public const H2 = 'Eine Person baut Ihre Website. Dieselbe antwortet danach.';

    /** §5 Sektion 6, gebundener Textlink. */
    public const LINK_TEXT = 'Mehr über SARTU';

    public const LINK_ZIEL = '/ueber-uns';

    /** Die Adresse, unter der das hinterlegte Bild ausgeliefert wird. */
    public const BILD_PFAD = '/bild/gruender';

    /**
     * Die Markierung im Platzhalter — `10_WEBSITE_SARTU.md` §5, Bedingung 4a.
     *
     * Sie steht im **ausgelieferten Markup** und nicht nur im Kommentar. Genau daran findet
     * `Platzhalterpruefung` sie, und `bin/startklar.php` bricht daran ab. Eine freie
     * Formulierung wie „Foto folgt" fände niemand wieder — §5: „**Keine freien
     * Formulierungen wie ‚TODO' oder ‚Lorem ipsum'**."
     */
    public const MARKIERUNG = '[[FOTO-FEHLT]]';

    /** Der Satz neben der Markierung — er sagt, wo die Angaben herkommen. */
    public const PLATZHALTERSATZ = 'Name, Foto und der Absatz stehen in den Betreiberdaten '
        . 'unter Einstellungen. Solange eine der drei Angaben fehlt, geht die Seite nicht '
        . 'produktiv.';

    public function __construct(private readonly ?BetreiberdatenSpeicher $betrieb = null)
    {
    }

    /**
     * Name, Absatz und Bildadresse — oder `null`, wenn eine der drei Angaben fehlt.
     *
     * @return array{name:string,text:string,bild:string}|null
     */
    public function angaben(): ?array
    {
        try {
            $zeile = ($this->betrieb ?? new BetreiberdatenSpeicher())->lesen();
        } catch (\Throwable) {
            // Ohne Datenbank steht die Seite trotzdem — dann eben ohne diese Sektion.
            return null;
        }

        if ($zeile === null) {
            return null;
        }

        $name = self::gefuellt($zeile['gruender_name'] ?? null);
        $text = self::gefuellt($zeile['gruender_text'] ?? null);
        $bild = self::gefuellt($zeile['gruender_bild'] ?? null);

        if ($name === null || $text === null || $bild === null) {
            return null;
        }

        return ['name' => $name, 'text' => $text, 'bild' => self::BILD_PFAD];
    }

    /**
     * Was an der Sektion noch fehlt — eine Zeile je fehlender Angabe, leer heisst vollständig.
     *
     * ## Warum die Sektion seit dem 13.08.2026 auch ohne Daten steht
     *
     * §4c liess sie entfallen, solange eine der drei Angaben fehlte. Der Betreiber hat das am
     * 13.08.2026 geändert (§4d): Sie steht mit **gekennzeichnetem Platzhalter**.
     *
     * **Das ist kein Rückschritt hinter §5, sondern die Auflösung, die §5 des
     * Website-Lastenhefts selbst vorsieht.** Bedingung 4a der Startsperre nennt genau diesen
     * Fall — „kein Foto hinterlegt, oder der Text enthält `[[FOTO-FEHLT]]`" — und bricht die
     * produktive Veröffentlichung daran ab. Der leere Rahmen ist damit eine **Zwischenstufe
     * im Bau**, nicht ein Zustand, der live gehen kann.
     *
     * Vorausgesetzt, jemand sucht die Markierung wirklich. Bis zum 13.08.2026 tat das
     * niemand; `Platzhalterpruefung` und `bin/startklar.php` schliessen die Lücke.
     *
     * @return list<string>
     */
    public function fehlendeAngaben(): array
    {
        try {
            $zeile = ($this->betrieb ?? new BetreiberdatenSpeicher())->lesen();
        } catch (\Throwable) {
            $zeile = null;
        }

        $felder = [
            'gruender_name' => 'der Name',
            'gruender_text' => 'der Absatz, warum es SARTU gibt',
            'gruender_bild' => 'das Foto',
        ];

        $fehlend = [];

        foreach ($felder as $feld => $beschriftung) {
            if (self::gefuellt($zeile[$feld] ?? null) === null) {
                $fehlend[] = $beschriftung;
            }
        }

        return $fehlend;
    }

    /**
     * Der Absatz in Absätzen.
     *
     * Der Betreiber tippt in ein Textfeld; wo er die Eingabetaste drückt, will er einen
     * Absatz. Ohne diese Zerlegung stünde sein Text als eine Wand da — und mit `nl2br()`
     * stünde er als eine Wand mit Zeilenumbrüchen da.
     *
     * @return list<string>
     */
    public static function absaetze(string $text): array
    {
        $teile = preg_split('/\R{2,}/', trim($text)) ?: [];

        $absaetze = [];
        foreach ($teile as $teil) {
            $sauber = trim((string) preg_replace('/\R+/', ' ', $teil));
            if ($sauber !== '') {
                $absaetze[] = $sauber;
            }
        }

        return $absaetze;
    }

    /**
     * Die Bildbeschreibung.
     *
     * §16 des Website-Lastenhefts: „Bildbeschreibungen beschreiben das Bild, nicht das
     * Suchwort. Sie sind öffentlich und werden vorgelesen." Sie trägt deshalb den Namen und
     * die Rolle — mehr weiß niemand über eine Datei, die der Betreiber hochgeladen hat.
     */
    public static function bildbeschreibung(string $name): string
    {
        return $name . ', gründergeführt bei SARTU.';
    }

    private static function gefuellt(mixed $wert): ?string
    {
        return is_string($wert) && trim($wert) !== '' ? trim($wert) : null;
    }
}
