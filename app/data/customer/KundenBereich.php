<?php

declare(strict_types=1);

namespace Sartu\Data\Customer;

use Sartu\Data\FehlendeOrganisation;
use Sartu\Sitzung;

/**
 * Die Organisation der laufenden Sitzung — Portal-Lastenheft §3 Regel 1 und 2a.
 *
 * Absichtlich eng gebaut:
 *
 *   - Der Konstruktor ist privat. Es gibt genau eine Fabrik, und die liest die Sitzung.
 *   - Es gibt KEINEN Parameter, mit dem sich die Organisation von aussen setzen liesse.
 *     Genau daran scheitert der uebliche Umgehungsversuch ueber ein Formularfeld.
 *   - Fehlt der Sitzungswert, fliegt eine Ausnahme. Kein Vorgabewert, kein „alles".
 *
 * Jede Klasse in diesem Verzeichnis verlangt dieses Objekt im Konstruktor. Damit gibt es
 * keinen Kundenzugriff ohne Organisation — nicht als Absicht, sondern als Signatur.
 */
final class KundenBereich
{
    private function __construct(
        public readonly string $organisationId,
    ) {
    }

    public static function ausSitzung(): self
    {
        $organisation = Sitzung::wert(Sitzung::ORGANISATION);

        if ($organisation === null) {
            throw new FehlendeOrganisation();
        }

        return new self($organisation);
    }
}
