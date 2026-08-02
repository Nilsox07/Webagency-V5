<?php

declare(strict_types=1);

namespace Sartu\Portal;

use Sartu\Antwort;
use Sartu\Data\Customer\KundenBereich;
use Sartu\Helpers\Http;
use Sartu\Services\Angebotsannahme;
use Sartu\Sitzung;

/**
 * Die Angebotsannahme — Portal-Lastenheft §8.2.
 *
 * Sie steht in einer eigenen Steuerung und nicht bei der Anzeige: Die Anzeige liest, die
 * Annahme ist eine Erklaerung mit Rechtsfolge. Zwei verschiedene Dinge in einer Datei
 * verleiten dazu, die Pruefungen der einen fuer die andere mitzubenutzen.
 */
final class AngebotSteuerung
{
    /** @param array<string,string> $parameter */
    public function annehmen(array $parameter = []): Antwort
    {
        $bereich = KundenBereich::ausSitzung();

        $fehler = (new Angebotsannahme($bereich))->annehmen(
            (string) ($parameter['id'] ?? ''),
            $_POST,
            (string) Sitzung::wert(Sitzung::BENUTZER),
            Http::gegenstelle() === '' ? null : Http::gegenstelle(),
        );

        if ($fehler === []) {
            return Antwort::weiter('/portal/angebot', 303);
        }

        // Die Fehler stehen auf der Angebotsseite selbst — der Kunde soll das Angebot dabei
        // sehen, nicht auf einer leeren Seite raten, was er falsch gemacht hat.
        return (new PortalSteuerung())->angebot([], $fehler);
    }
}
