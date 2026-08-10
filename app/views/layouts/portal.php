<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Html;

/**
 * Das Layout des Kundenbereichs — `design/portalkonzept.html`, abgenommen 03.08.2026.
 *
 * ## Der Aufbau, so wie er abgenommen wurde
 *
 * Seitenleiste links in Tinte, Arbeitsflaeche rechts mit Titelzeile oben. Die **Aussenkante
 * bleibt rechtwinklig** — der Bereich fuellt das Fenster, und eine Rundung aussen gaebe es
 * nicht zu sehen. Gerundet ist allein die **rechte Kante der Leiste**.
 *
 * Bis zum 10.08.2026 stand hier ein waagerechtes Kopfband ueber voller Breite.
 *
 * ## Warum die Leiste vor dem Inhalt steht und trotzdem uebersprungen werden kann
 *
 * Die Sprungmarke fuehrt an ihr vorbei. Wer mit der Tastatur arbeitet, soll nicht neun
 * Menuepunkte durchlaufen, um zum ersten Absatz zu kommen.
 *
 * §8: „Seitentitel im <title> als {Seite} — SARTU-Portal". Nach aussen heisst der Bereich
 * Kundenbereich (`CLAUDE.md`), deshalb steht dort `{Seite} — SARTU-Kundenbereich`.
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
<title><?= Html::e($titel) ?> — SARTU-Kundenbereich</title>
<link rel="stylesheet" href="/assets/css/tokens.css">
<link rel="stylesheet" href="/assets/css/anwendung.css">
</head>
<body class="bereichseite">
<?= Ansicht::teil('partials/zeichen') ?>
<a class="sprungmarke" href="#inhalt">Zum Inhalt springen</a>
<div class="bereich">
<?= Ansicht::teil('partials/kundenband', [
    'angemeldet' => $angemeldet ?? false,
    'pfad'       => Ansicht::pfad(),
    'zaehler'    => $zaehler ?? [],
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
