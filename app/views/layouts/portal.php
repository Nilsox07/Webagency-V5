<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Html;

/**
 * Das Layout des Kundenbereichs.
 *
 * **Kunden- und Adminbereich muessen visuell unterscheidbar sein** (`CLAUDE.md`,
 * CODEX_AUFTRAG_PORTAL.md §4). Der interne Bereich traegt ein dunkles Kopfband; hier steht
 * ein helles mit Lime-Kante. Beide benutzen dieselben Gestaltungswerte — der Unterschied
 * liegt in der Flaeche, nicht in einer zweiten Farbwelt.
 *
 * §8: „Seitentitel im <title> als {Seite} — SARTU-Portal". Nach aussen heisst der Bereich
 * Kundenbereich (`CLAUDE.md`), deshalb steht dort `{Seite} — SARTU-Kundenbereich`.
 *
 * @var string $titel
 * @var string $inhalt
 */

?><!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= Html::e($titel) ?> — SARTU-Kundenbereich</title>
<link rel="stylesheet" href="/assets/css/tokens.css">
<link rel="stylesheet" href="/assets/css/anwendung.css">
</head>
<body>
<?= Ansicht::teil('partials/kundenband', ['angemeldet' => $angemeldet ?? false]) ?>
<main class="bahn schmal">
<?= $inhalt ?>
</main>
<?= Ansicht::teil('partials/fuss') ?>
</body>
</html>
