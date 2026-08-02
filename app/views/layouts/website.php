<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Env;
use Sartu\Helpers\Html;

/**
 * Das Layout der öffentlichen Website — Website-Lastenheft §1, §3, §4, §16.
 *
 * **Getrennt vom Layout `oeffentlich`**, das nur die drei Rechtstextrouten trägt. Dort gibt
 * es weder Navigation noch Fußspalten, und die Rechtstexte sollen keine bekommen: Sie werden
 * gelesen, nicht durchgeblättert.
 *
 * ## Was jede Seite mitbringen muss (§17, „Technik und SEO")
 *
 * Status 200 · genau eine H1 · eigener Titel und eigene Beschreibung · Canonical auf sich
 * selbst · Breadcrumb. Die H1 steht in der Seitenansicht, alles andere hier — dadurch kann
 * eine Seite es nicht vergessen.
 *
 * ## Kein Skript, keine fremde Verbindung
 *
 * §1: „Kein externes CDN für Schriften, CSS oder JS." Es gibt hier kein ausführbares Skript
 * und keine Adresse, die auf eine fremde Domain zeigt. Das mobile Menü ist ein
 * `details`-Element — es öffnet und schließt ohne JavaScript, hält den Fokus von sich aus
 * und schließt mit `Esc`.
 *
 * **Der eine Datenblock unten ist kein Skript.** `application/ld+json` wird nie ausgeführt;
 * `script-src` greift darauf nicht zu. §16 verlangt strukturierte Daten, und es gibt keine
 * andere Form, sie auszuliefern. `SecurityHeadersTest` prüft, dass genau dieser eine Typ
 * vorkommt und dass sein Inhalt keine rohe spitze Klammer enthält.
 *
 * @var string $titel
 * @var string $inhalt
 * @var string|null $beschreibung
 * @var string $pfad
 * @var list<array{0:string,1:string}> $brotkrumen  je [Adresse, Beschriftung]
 * @var bool $noindex
 * @var string|null $schema  fertiges JSON-LD, vom Aufrufer erzeugt
 * @var bool $kleinunternehmer  aus `operator_settings`, nie aus dem Quelltext
 */

$brotkrumen = $brotkrumen ?? [];
$pfad = $pfad ?? '/';
$basis = rtrim((string) Env::get('BASE_URL', ''), '/');

?><!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= Html::e($titel) ?></title>
<?php if (isset($beschreibung) && $beschreibung !== null): ?>
<meta name="description" content="<?= Html::e($beschreibung) ?>">
<?php endif; ?>
<?php if (($noindex ?? false) === true): ?>
<meta name="robots" content="noindex, follow">
<?php endif; ?>
<link rel="canonical" href="<?= Html::e($basis . $pfad) ?>">
<link rel="stylesheet" href="/assets/css/tokens.css">
<link rel="stylesheet" href="/assets/css/anwendung.css">
<link rel="stylesheet" href="/assets/css/website.css">
<?php if (isset($schema) && $schema !== null): ?>
<script type="application/ld+json"><?= $schema ?></script>
<?php endif; ?>
</head>
<body>
<a class="sprungmarke" href="#inhalt">Zum Inhalt springen</a>
<?= Ansicht::teil('partials/websiteband', ['pfad' => $pfad]) ?>
<main id="inhalt">
<?php if ($brotkrumen !== []): ?>
<nav class="brotkrumen bahn" aria-label="Sie sind hier">
  <ol>
    <li><a href="/">Start</a></li>
<?php foreach ($brotkrumen as $krume): ?>
    <li><a href="<?= Html::e($krume[0]) ?>"><?= Html::e($krume[1]) ?></a></li>
<?php endforeach; ?>
  </ol>
</nav>
<?php endif; ?>
<?= $inhalt ?>
</main>
<?= Ansicht::teil('partials/websitefuss', ['kleinunternehmer' => $kleinunternehmer ?? false]) ?>
</body>
</html>
