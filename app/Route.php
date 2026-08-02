<?php

declare(strict_types=1);

namespace Sartu;

final class Route
{
    public const BEREICH_OEFFENTLICH = 'oeffentlich';
    public const BEREICH_PORTAL      = 'portal';
    public const BEREICH_ADMIN       = 'admin';
    public const BEREICH_API         = 'api';

    /** @param callable():Antwort|array{0:class-string,1:string} $handler */
    public function __construct(
        public readonly string $bereich,
        public readonly string $methode,
        public readonly string $pfad,
        public readonly mixed $handler,
        /**
         * Nur fuer die Ersteinrichtung. Sie ist die einzige Adminroute ohne Anmeldung —
         * es gibt zu diesem Zeitpunkt noch kein Konto. Ihre eigene Sperre steht in
         * InstallationsSperre und ist strenger als jede Anmeldung (§1.5).
         */
        public readonly bool $ohneAnmeldung = false,
    ) {
    }

    public function schluessel(): string
    {
        return $this->methode . ' ' . $this->pfad;
    }
}
