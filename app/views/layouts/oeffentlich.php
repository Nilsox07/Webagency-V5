<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Html;

/** @var string $titel */
/** @var string $inhalt */
/** @var string|null $beschreibung */

?><!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= Html::e($titel) ?> | SARTU</title>
<?php if (isset($beschreibung) && $beschreibung !== null): ?>
<meta name="description" content="<?= Html::e($beschreibung) ?>">
<?php endif; ?>
<?php /* Website-Lastenheft §9: die Ergebnisschritte und die Danke-Seite sind `noindex`. */ ?>
<?php if (($noindex ?? false) === true): ?>
<meta name="robots" content="noindex, follow">
<?php endif; ?>
<?php /* §17: Canonical auf jeder Seite. Fehlt der Pfad, gibt es keins — geraten wird nicht. */ ?>
<?php if (isset($pfad)): ?>
<link rel="canonical" href="<?= Html::e(rtrim((string) \Sartu\Helpers\Env::get('BASE_URL', ''), '/') . $pfad) ?>">
<?php endif; ?>
<link rel="stylesheet" href="/assets/css/tokens.css">
<link rel="stylesheet" href="/assets/css/anwendung.css">
</head>
<body>
<main class="bahn schmal">
<?= $inhalt ?>
</main>
<?= Ansicht::teil('partials/fuss') ?>
</body>
</html>
