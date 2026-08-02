<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/**
 * Willkommensstrecke, Bildschirm 2 von 3 — Portal-Lastenheft §7.
 *
 * Zwei Spalten, mobil untereinander. Die zwoelf Zeilen stehen dort woertlich und werden
 * nicht gekuerzt: Sie sind der Punkt des Bildschirms — die Arbeitsteilung als Liste, nicht
 * als Satz mit zwoelf Gliedern (`SARTU_TEXTREGELN.md` Regel 3).
 */

$machen = [
    'Angebot ansehen und annehmen',
    'Rechnungen bezahlen',
    'Fragen zu Ihrem Betrieb beantworten',
    'Bilder und Unterlagen hochladen',
    'Vorschau ansehen und freigeben',
    'Später Öffnungszeiten ändern',
];

$muessenNicht = [
    'Technik verstehen',
    'Seiten selbst bauen',
    'Webtexte schreiben',
    'Wissen, wie viele Seiten Sie brauchen',
    'Irgendetwas installieren',
    'Sich um Updates oder Sicherheit kümmern',
];

?>
<p class="vorzeile">Bildschirm 2 von 3</p>
<h1>Das machen Sie hier.</h1>

<div class="spaltenpaar">
  <div>
    <h2>Das machen Sie hier</h2>
    <ul class="liste liste--einfach">
<?php foreach ($machen as $punkt): ?>
      <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
    </ul>
  </div>
  <div>
    <h2>Das müssen Sie nicht</h2>
    <ul class="liste liste--einfach">
<?php foreach ($muessenNicht as $punkt): ?>
      <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
    </ul>
  </div>
</div>

<p>Struktur, Design, Technik und die Suchmaschinen-Grundlage übernehmen wir. Sie liefern die
Fakten aus Ihrem Betrieb — den Rest machen wir.</p>

<div class="knopfreihe">
  <a class="knopf" href="/willkommen/3">Weiter</a>
  <a class="knopf knopf--ruhig" href="/willkommen/1">Zurück</a>
</div>

<?= \Sartu\Ansicht::teil('partials/willkommen-ueberspringen') ?>
