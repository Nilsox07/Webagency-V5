<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Helpers\Env;

/**
 * Der Schluessel des Zahlungsdienstes — `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 6, Fall 93.
 *
 * ## Verschluesselt wie das TOTP-Geheimnis
 *
 * §6: „Der Schluessel wird mit `sodium_*` verschluesselt abgelegt, wie das TOTP-Geheimnis.
 * Er erscheint **nie** im Klartext — nicht in einer Ansicht, nicht in einer Fehlermeldung,
 * nicht im Protokoll."
 *
 * Diese Klasse ist deshalb die **einzige** Stelle, an der der Klartext ueberhaupt entsteht,
 * und sie gibt ihn nur an einer Stelle heraus: an `Mollie`, der ihn in eine Kopfzeile setzt.
 * Fuer alles andere gibt es `hinterlegt()` (ja/nein) und `spur()` (die letzten vier
 * Zeichen). Eine Methode `lesen()` mit oeffentlichem Klartext gibt es nicht — sie waere die
 * Stelle, an der jemand ihn in eine Ansicht schreibt, ohne es zu merken.
 *
 * ## Welcher der beiden gilt, entscheidet nicht die Oberflaeche
 *
 * §6: „Test- und Produktivschluessel sind getrennte Felder; welcher gilt, entscheidet
 * `APP_ENV`, nicht ein Schalter in der Oberflaeche." Ein Schalter waere ein Klick zwischen
 * Testbetrieb und echtem Geld.
 *
 * **Fehlt `APP_ENV`, gilt produktiv** — dieselbe Vorsichtsrichtung wie bei der
 * HTTP-Ausnahme der Ersteinrichtung (§14 Sicherheit). Wer sich vertut, arbeitet dann mit
 * einem fehlenden Produktivschluessel und merkt es sofort, statt still im Testbetrieb Geld
 * zu erwarten, das nie kommt.
 */
final class Zahlungsschluessel
{
    public const FELD_TEST = 'mollie_key_test';
    public const FELD_LIVE = 'mollie_key_live';

    /**
     * Die kuerzeste Form, die Mollie vergibt, ist `test_` bzw. `live_` und danach ein
     * Zufallsteil. Kuerzer als das ist kein Schluessel, sondern ein Tippfehler.
     */
    public const MINDESTLAENGE = 20;

    public function __construct(
        private readonly ?BetreiberdatenSpeicher $betreiber = null,
        private readonly ?Verschluesselung $tresor = null,
    ) {
    }

    /** Welches Feld in dieser Umgebung gilt. */
    public static function feld(): string
    {
        return Env::get('APP_ENV', 'production') === 'local' ? self::FELD_TEST : self::FELD_LIVE;
    }

    /**
     * Legt einen Schluessel ab.
     *
     * @return list<string> leer bei Erfolg
     */
    public function hinterlegen(string $feld, string $klartext): array
    {
        if (!in_array($feld, BetreiberdatenSpeicher::SCHLUESSELFELDER, true)) {
            return ['Dieses Feld gibt es nicht.'];
        }

        $klartext = trim($klartext);

        if ($klartext === '') {
            return ['Bitte tragen Sie den Schlüssel ein.'];
        }

        if (mb_strlen($klartext) < self::MINDESTLAENGE) {
            // **Der eingegebene Wert steht nicht in der Meldung.** Eine Fehlermeldung, die
            // den abgewiesenen Schlüssel wiederholt, ist genau die Stelle aus Fall 93.
            return ['Dieser Schlüssel ist zu kurz. Bitte prüfen Sie, ob er vollständig kopiert wurde.'];
        }

        $erwartet = $feld === self::FELD_TEST ? 'test_' : 'live_';

        if (!str_starts_with($klartext, $erwartet)) {
            return [$feld === self::FELD_TEST
                ? 'Ein Testschlüssel beginnt mit „test_".'
                : 'Ein Produktivschlüssel beginnt mit „live_".'];
        }

        $this->betreiber()->schluesselSetzen($feld, $this->tresor()->verschluesseln($klartext));

        return [];
    }

    public function entfernen(string $feld): void
    {
        $this->betreiber()->schluesselSetzen($feld, null);
    }

    public function hinterlegt(?string $feld = null): bool
    {
        return $this->betreiber()->schluesselLesen($feld ?? self::feld()) !== null;
    }

    /**
     * Die letzten vier Zeichen — das Einzige, was eine Ansicht je zu sehen bekommt.
     *
     * Vier Zeichen genuegen, um zwei Schluessel zu unterscheiden, und reichen nicht, um
     * einen zu benutzen. Ohne diese Spur muesste der Betreiber den Schluessel neu
     * hinterlegen, nur um zu wissen, welcher dasteht.
     */
    public function spur(?string $feld = null): ?string
    {
        $klartext = $this->klartext($feld);

        return $klartext === null ? null : '…' . mb_substr($klartext, -4);
    }

    /**
     * Der Klartext. **Nur fuer den Aufruf beim Zahlungsdienst.**
     *
     * `@internal` ist hier keine Formalie: Jeder andere Aufrufer waere ein Weg, den
     * Schluessel irgendwohin zu schreiben.
     *
     * @internal
     */
    public function klartext(?string $feld = null): ?string
    {
        $kasten = $this->betreiber()->schluesselLesen($feld ?? self::feld());

        if ($kasten === null) {
            return null;
        }

        try {
            return $this->tresor()->entschluesseln($kasten);
        } catch (\Throwable) {
            // Der Kasten passt nicht zu ENC_KEY. Die Ausnahme wird **nicht** weitergereicht:
            // Ihr Text nennt zwar keinen Schluessel, aber ein Aufrufer, der sie faengt und
            // anzeigt, zeigt eine Meldung ueber ein Geheimnis. Fehlend und unlesbar sind
            // fuer jeden Aufrufer hier dasselbe — es gibt keinen Schluessel zum Arbeiten.
            return null;
        }
    }

    private function betreiber(): BetreiberdatenSpeicher
    {
        return $this->betreiber ?? new BetreiberdatenSpeicher();
    }

    private function tresor(): Verschluesselung
    {
        return $this->tresor ?? new Verschluesselung();
    }
}
