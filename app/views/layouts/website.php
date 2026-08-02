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
 * ## Ein Skript, aus dem eigenen Verzeichnis
 *
 * §1: „Kein externes CDN für Schriften, CSS oder JS." Keine Adresse hier zeigt auf eine
 * fremde Domain. Das mobile Menü ist ein `details`-Element und öffnet, schließt und lässt
 * sich mit der Tastatur bedienen, **ohne** dass ein Skript läuft.
 *
 * `menue.js` fügt allein die Fokusfalle aus §3 hinzu — mit `defer`, ohne Zeileninhalt, über
 * `script-src 'self'`. Fällt es aus, bleibt das Menü vollständig bedienbar. Die Begründung
 * steht im Kopf der Datei; die Entscheidung, sie zu bauen, in `OFFENE_ENTSCHEIDUNGEN.md`.
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
<script src="/assets/js/menue.js" defer></script>
</body>
</html>
