<?php

declare(strict_types=1);

namespace Sartu\Data;

/**
 * Traegt die Nummer der gescheiterten Migration mit — Portal-Lastenheft §1.5 verlangt sie
 * ausdruecklich in der Klartextmeldung.
 */
final class MigrationFehler extends \RuntimeException
{
    public function __construct(
        string $meldung,
        public readonly ?string $version = null,
        ?\Throwable $ursache = null,
    ) {
        parent::__construct($meldung, 0, $ursache);
    }
}
