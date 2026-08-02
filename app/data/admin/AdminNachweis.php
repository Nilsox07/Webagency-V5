<?php

declare(strict_types=1);

namespace Sartu\Data\Admin;

use Sartu\Services\InstallationsSperre;
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
    /** Kennung des Nachweises, den die Ersteinrichtung benutzt. Kein echtes Konto. */
    private const EINRICHTUNG = '00000000-0000-4000-8000-000000000000';

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
     * Nur waehrend der Ersteinrichtung (§1.5 Schritte 6 bis 8).
     *
     * Zu diesem Zeitpunkt existiert noch kein Konto, mit dem sich jemand anmelden koennte.
     * Die Strecke hat stattdessen ihre eigene, strengere Sperre — und **die** wird hier
     * gefragt, nicht der Aufrufer.
     *
     * Vorher stand hier ein Parameter `bool $einrichtungLaeuft`, der ausnahmslos mit `true`
     * uebergeben wurde. Eine Bedingung, die der Aufrufer selbst setzt, ist keine.
     */
    public static function fuerErsteinrichtung(?InstallationsSperre $sperre = null): self
    {
        if (($sperre ?? new InstallationsSperre())->gesperrt()) {
            throw new \LogicException(
                'Ein Nachweis fuer die Ersteinrichtung ist nach ihrem Abschluss nicht mehr zulaessig.'
            );
        }

        return new self(self::EINRICHTUNG);
    }

    public function istEinrichtung(): bool
    {
        return $this->adminBenutzerId === self::EINRICHTUNG;
    }
}
