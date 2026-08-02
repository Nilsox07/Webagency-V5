<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Html;

/** @var string $titel */
/** @var string $inhalt */
/** @var bool $angemeldet */

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
<?= Ansicht::teil('partials/kopfband', ['angemeldet' => $angemeldet ?? false]) ?>
<main class="bahn">
<?= $inhalt ?>
</main>
<?= Ansicht::teil('partials/fuss') ?>
</body>
</html>
