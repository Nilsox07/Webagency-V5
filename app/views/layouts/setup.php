<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Html;

/**
 * Eigenes Grundgeruest fuer die Ersteinrichtung.
 *
 * Ohne Navigation: Waehrend der Einrichtung gibt es keinen anderen Ort, an den man gehen
 * koennte — jeder Aufruf ausser dieser Strecke leitet hierher zurueck (§1.5).
 */

/** @var string $titel */
/** @var string $inhalt */
/** @var int|null $schritt */

?><!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= Html::e($titel) ?> | SARTU</title>
<link rel="stylesheet" href="/assets/css/tokens.css">
<link rel="stylesheet" href="/assets/css/anwendung.css">
</head>
<body>
<header class="kopfband">
  <div class="bahn schmal">
    <span class="wortmarke">SARTU</span>
  </div>
</header>
<main class="bahn schmal">
<?php if (isset($schritt) && $schritt !== null): ?>
<?= Ansicht::teil('partials/schrittleiste', ['schritt' => $schritt]) ?>
<?php endif; ?>
<?= $inhalt ?>
</main>
<?= Ansicht::teil('partials/fuss') ?>
</body>
</html>
