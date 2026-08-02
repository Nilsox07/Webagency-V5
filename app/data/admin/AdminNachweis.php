<?php

declare(strict_types=1);

namespace Sartu\Data\Admin;

use Sartu\Sitzung;

/**
 * Der Nachweis, dass eine bestandene Adminpruefung vorliegt — Portal-Lastenheft §3 Regel 2a.
 *
 * Jede Klasse in diesem Verzeichnis verlangt ihn im Konstruktor. Damit ist
 * organisationsuebergreifendes Lesen nicht eine Frage der Disziplin, sondern der Signatur:
 * Wer keinen Nachweis hat, kann die Klasse nicht bauen.
 *
 * Rolle allein genuegt nicht. Ohne abgeschlossene Zweifaktor-Anmeldung entsteht kein
 * Nachweis (Testfall 44).
 */
final class AdminNachweis
{
    private function __construct(
        public readonly string $adminBenutzerId,
    ) {
    }

    public static function ausSitzung(): ?self
    {
        if (!Sitzung::istAngemeldeterAdmin()) {
            return null;
        }

        $benutzer = Sitzung::wert(Sitzung::BENUTZER);

        return $benutzer === null ? null : new self($benutzer);
    }

    /**
     * Nur fuer die Ersteinrichtung (§1.5 Schritte 6 bis 8). Zu diesem Zeitpunkt existiert
     * noch kein Konto, mit dem sich jemand anmelden koennte — die Strecke hat stattdessen
     * ihre eigene, strengere Sperre (InstallationsSperre).
     *
     * Ausserhalb der Ersteinrichtung wirft die Methode.
     */
    public static function fuerErsteinrichtung(bool $einrichtungLaeuft): self
    {
        if (!$einrichtungLaeuft) {
            throw new \LogicException('Ein Nachweis fuer die Ersteinrichtung ist nur waehrend der Ersteinrichtung zulaessig.');
        }

        return new self('00000000-0000-4000-8000-000000000000');
    }

    public function istEinrichtung(): bool
    {
        return $this->adminBenutzerId === '00000000-0000-4000-8000-000000000000';
    }
}
