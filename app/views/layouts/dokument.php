<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Env;
use Sartu\Helpers\Html;

/**
 * Das Layout der Rechtstexte — die **dokumentarische** Seitengattung.
 *
 * ## Warum es dieses Layout gibt
 *
 * Impressum, Datenschutz und AGB liefen bis zum 16.08.2026 über `layouts/oeffentlich`: eine
 * 46-rem-Spalte ohne Kopf, mit einer Fusszeile aus zwei Verweisen. Ein Impressum, das nicht
 * erkennbar zur Website gehört, erfüllt § 5 DDG schlechter, als es könnte — die Angaben
 * müssen „leicht erkennbar, unmittelbar erreichbar und ständig verfügbar" sein, und dazu
 * gehört, dass der Leser sieht, **wessen** Impressum er liest.
 *
 * ## Was diese Gattung ausmacht
 *
 * Marke und Navigation wie auf der Website, damit die Seite zugehörig ist. Danach eine
 * ruhige Lesespalte von rund 70 Zeichen, klare Überschriftenhierarchie, der Änderungsstand
 * am Anfang — und **keine Verkaufsfläche**: kein Aufmacher, kein Handlungsfeld, kein
 * Bedarfsscheck-Knopf. Wer ein Impressum liest, sucht eine Angabe, kein Angebot.
 *
 * Der Fussbereich ist der vollständige der Website: Er trägt die Pflichtangaben nach §1.4a,
 * und die gehören unter einen Rechtstext.
 *
 * @var string $titel
 * @var string $inhalt
 * @var string|null $beschreibung
 * @var string|null $pfad
 * @var bool $noindex
 * @var bool $kleinunternehmer
 */

$pfad = $pfad ?? null;
$basis = rtrim((string) Env::get('BASE_URL', ''), '/');

?><!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= Html::e($titel) ?> | SARTU</title>
<?php if (isset($beschreibung) && $beschreibung !== null): ?>
<meta name="description" content="<?= Html::e($beschreibung) ?>">
<?php endif; ?>
<?php if (($noindex ?? false) === true): ?>
<meta name="robots" content="noindex, follow">
<?php endif; ?>
<?php if ($pfad !== null): ?>
<link rel="canonical" href="<?= Html::e($basis . $pfad) ?>">
<?php endif; ?>
<?= Ansicht::teil('partials/markenzeichen') ?>
<?= Ansicht::teil('partials/teilenkarte', [
    'titel'        => $titel . ' | SARTU',
    'beschreibung' => $beschreibung ?? null,
    'adresse'      => $basis . ($pfad ?? '/'),
    'basis'        => $basis,
]) ?>
<link rel="stylesheet" href="/assets/css/tokens.css">
<link rel="stylesheet" href="/assets/css/anwendung.css">
<link rel="stylesheet" href="/assets/css/website.css">
</head>
<body>
<a class="sprungmarke" href="#inhalt">Zum Inhalt springen</a>
<?= Ansicht::teil('partials/websiteband', ['pfad' => $pfad ?? '']) ?>

<main id="inhalt" class="dokument">
  <div class="bahn dokument__satz">
<?= $inhalt ?>
  </div>
</main>

<?= Ansicht::teil('partials/websitefuss', ['kleinunternehmer' => $kleinunternehmer ?? false]) ?>
<script src="/assets/js/menue.js" defer></script>
</body>
</html>
