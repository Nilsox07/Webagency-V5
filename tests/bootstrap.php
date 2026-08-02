<?php

declare(strict_types=1);

/**
 * Testlauf. Verbindet auf db_test — nie auf die Arbeitsdatenbank.
 */

$wurzel = require dirname(__DIR__) . '/app/bootstrap.php';

restore_exception_handler();

// Ohne das laufen die Ansichten in einen Fehler, sobald sie ein CSRF-Feld bauen.
if (session_status() !== PHP_SESSION_ACTIVE) {
    $_SESSION = [];
}

define('SARTU_WURZEL', $wurzel);
