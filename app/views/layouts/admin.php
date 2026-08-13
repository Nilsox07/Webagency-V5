<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Html;

/**
 * Das Layout des internen Bereichs — dieselbe Huelle wie der Kundenbereich, dichter gesetzt.
 *
 * `CODEX_AUFTRAG_PORTAL.md` §4 verlangt, dass beide Bereiche unterscheidbar sind. Der
 * Unterschied steht in `partials/kopfband.php` und in der Klasse `bereich--intern`:
 * hellere Leiste, kleinere Grundschrift, flachere Karten, mehr Zeilen je Bildschirm.
 *
 * @var string $titel
 * @var string $inhalt
 * @var bool $angemeldet
 */

?><!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= Html::e($titel) ?> | SARTU</title>
<?= Ansicht::teil('partials/markenzeichen') ?>
<link rel="stylesheet" href="/assets/css/tokens.css">
<link rel="stylesheet" href="/assets/css/anwendung.css">
</head>
<body class="bereichseite">
<?= Ansicht::teil('partials/zeichen') ?>
<a class="sprungmarke" href="#inhalt">Zum Inhalt springen</a>
<div class="bereich bereich--intern">
<?= Ansicht::teil('partials/kopfband', [
    'angemeldet' => $angemeldet ?? false,
    'pfad'       => Ansicht::pfad(),
]) ?>
  <div class="flaeche">
    <header class="titelzeile">
      <h1><?= Html::e($titel) ?></h1>
    </header>
    <main class="flaeche__inhalt" id="inhalt">
<?= $inhalt ?>
    </main>
  </div>
</div>
</body>
</html>
