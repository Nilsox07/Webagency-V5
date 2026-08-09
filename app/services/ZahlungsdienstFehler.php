<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Der Zahlungsdienst war nicht erreichbar oder hat abgelehnt.
 *
 * **In der Meldung steht nie der Schluessel** (Fall 93). Sie nennt, was nicht ging, und die
 * Kennung der Zahlung — beides darf in einem Protokoll stehen.
 */
final class ZahlungsdienstFehler extends \RuntimeException
{
}
