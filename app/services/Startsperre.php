<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\RechtstexteSpeicher;
use Sartu\Helpers\Validate;

/**
 * Die Startsperre aus Portal-Lastenheft §1.4a und Website-Lastenheft §14a.
 *
 * Sie prueft nicht auf Platzhalter in Vorlagen, sondern auf den Zustand der Einstellungen.
 * Die produktive Veroeffentlichung bricht ab, wenn:
 *
 *   - ein Pflichtfeld leer ist
 *   - weder ust_id noch steuernummer gesetzt ist
 *   - ein Rechtstext noch den Vermerk ENTWURF traegt
 *
 * „Abbruch, keine Warnung. Eine Warnung wird weggeklickt."
 *
 * Wichtig und leicht zu uebersehen: NOT NULL erlaubt eine leere Zeichenkette. Geprueft wird
 * deshalb nach trim() — nach derselben Regel, nach der auch gespeichert wird (Testfall 65).
 */
final class Startsperre
{
    public function __construct(
        private readonly ?BetreiberdatenSpeicher $betreiberdaten = null,
        private readonly ?RechtstexteSpeicher $rechtstexte = null,
    ) {
    }

    /** @return list<string> Leer bedeutet: der Start ist frei. */
    public function hindernisse(): array
    {
        $hindernisse = [];

        $daten = ($this->betreiberdaten ?? new BetreiberdatenSpeicher())->lesen();

        if ($daten === null) {
            return ['Die Betreiberdaten sind noch nicht angelegt.'];
        }

        foreach (BetreiberdatenSpeicher::PFLICHTFELDER as $feld) {
            $wert = $daten[$feld] ?? null;
            if (!Validate::gefuellt(is_string($wert) ? $wert : null)) {
                $hindernisse[] = sprintf(
                    'Das Pflichtfeld „%s" der Betreiberdaten ist leer.',
                    BetreiberdatenSpeicher::beschriftung($feld)
                );
            }
        }

        $ustId = is_string($daten['ust_id'] ?? null) ? $daten['ust_id'] : null;
        $steuernummer = is_string($daten['steuernummer'] ?? null) ? $daten['steuernummer'] : null;

        if (!Validate::gefuellt($ustId) && !Validate::gefuellt($steuernummer)) {
            $hindernisse[] = 'Es ist weder eine Umsatzsteuer-Identifikationsnummer noch eine Steuernummer hinterlegt.';
        }

        foreach (($this->rechtstexte ?? new RechtstexteSpeicher())->nichtFreigegebene() as $slug) {
            $hindernisse[] = sprintf(
                'Der Rechtstext „%s" ist noch nicht freigegeben.',
                RechtstexteSpeicher::beschriftung($slug)
            );
        }

        return $hindernisse;
    }

    public function starterlaubt(): bool
    {
        return $this->hindernisse() === [];
    }

    /**
     * Punkte, die **nicht** sperren — Stufe C, `18_BELEGE_UND_ZAHLUNG.md` §6 und §2.
     *
     * ## Warum sie nicht in `hindernisse()` stehen
     *
     * §1.4a zaehlt abschliessend auf, was die Veroeffentlichung anhaelt: leere Pflichtfelder,
     * fehlende Steuernummer, nicht freigegebene Rechtstexte. Etwas dazuzunehmen hiesse, die
     * Sperre zu verschaerfen, ohne dass eine Vorgabe das verlangt — ein Betrieb, der seine
     * Rechnungen per Ueberweisung stellt, braucht keinen Zahlungsdienst und darf trotzdem
     * online gehen.
     *
     * Sie hier zu verschweigen waere aber die andere Haelfte des Fehlers: Wer den
     * Zahlungsschluessel nie hinterlegt, merkt es erst, wenn die erste Rechnung ohne
     * Zahlungsweg beim Kunden liegt.
     *
     * Deshalb: eine zweite Liste, sichtbar an derselben Stelle, ohne Sperrwirkung.
     *
     * @return list<string>
     */
    public function weiterePunkte(): array
    {
        $punkte = [];

        if (!(new Zahlungsschluessel($this->betreiberdaten))->hinterlegt()) {
            $punkte[] = 'Für diese Umgebung ist kein Zahlungsschlüssel hinterlegt. '
                . 'Rechnungen gehen dann ohne Zahlungsweg hinaus — die Bankverbindung steht darauf.';
        }

        if (!self::logoAusgeliefert()) {
            $punkte[] = 'Das Logo fehlt im ausgelieferten Verzeichnis. '
                . 'Rechnungsbelege entstehen dann ohne Logo.';
        }

        return $punkte;
    }

    /**
     * Liegt das Logo dort, wo Auslieferung und Belegerzeugung es suchen?
     *
     * **Es wird nicht hochgeladen.** `07_MARKE_UND_GESTALTUNG.md` legt die Marke fest; die
     * Dateien liegen in `design/` und werden nach `public/assets/bild/` ausgeliefert. Ein
     * Feld zum Hochladen waere ein Weg, die Marke zu ersetzen — geprueft wird deshalb nur,
     * ob die Auslieferung stattgefunden hat.
     */
    public static function logoAusgeliefert(): bool
    {
        $wurzel = dirname(__DIR__, 2);

        foreach (['sartu-logo-hell.svg', 'sartu-logo-dunkel.svg', 'sartu-mark.svg'] as $datei) {
            if (!is_file($wurzel . '/public/assets/bild/' . $datei)) {
                return false;
            }
        }

        return true;
    }
}
