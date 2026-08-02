<?php

declare(strict_types=1);

namespace Sartu\Data;

/**
 * Portal-Lastenheft §3 Regel 2a: „Ein fehlender Session-Wert ist ein Fehler, kein
 * ‚alles anzeigen'."
 *
 * Diese Ausnahme ist der Unterschied zwischen einer leeren Liste und der Kundenliste eines
 * fremden Betriebs. Testfall 5b prueft, dass sie fliegt.
 */
final class FehlendeOrganisation extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Kundenabfrage ohne Organisation in der Sitzung.');
    }
}
