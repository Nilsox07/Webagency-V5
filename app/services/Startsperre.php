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
                $hindernisse[] = sprintf('Das Pflichtfeld „%s" der Betreiberdaten ist leer.', $feld);
            }
        }

        $ustId = is_string($daten['ust_id'] ?? null) ? $daten['ust_id'] : null;
        $steuernummer = is_string($daten['steuernummer'] ?? null) ? $daten['steuernummer'] : null;

        if (!Validate::gefuellt($ustId) && !Validate::gefuellt($steuernummer)) {
            $hindernisse[] = 'Es ist weder eine Umsatzsteuer-Identifikationsnummer noch eine Steuernummer hinterlegt.';
        }

        foreach (($this->rechtstexte ?? new RechtstexteSpeicher())->nichtFreigegebene() as $slug) {
            $hindernisse[] = sprintf('Der Rechtstext „%s" ist noch nicht freigegeben.', $slug);
        }

        return $hindernisse;
    }

    public function starterlaubt(): bool
    {
        return $this->hindernisse() === [];
    }
}
