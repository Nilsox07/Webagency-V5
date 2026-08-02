<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;

/** @var string $slug */
/** @var string $beschriftung */
/** @var array<string,mixed>|null $text */
/** @var list<string> $fehler */
/** @var list<string> $hinweise */
/** @var array<string,string> $zustaende */

$rumpf = is_array($text) ? (string) $text['body'] : '';
$zustand = is_array($text) ? (string) $text['status'] : 'entwurf';

?>
<h1><?= Html::e($beschriftung) ?></h1>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler, 'hinweise' => $hinweise]) ?>

<?php if (is_array($text)): ?>
<p>
  Zustand: <?= Html::e($zustaende[$zustand] ?? $zustand) ?> ·
  Fassung <?= Html::e((string) $text['version']) ?> ·
  Sichtbar für: <?= Html::e((string) $text['audience'] === 'kunde' ? 'angemeldete Kunden' : 'alle Besucher') ?>
</p>
<?php if ((string) $text['status'] === 'freigegeben'): ?>
<p class="leise">
  Freigegeben am <?= Html::e(Format::datum(is_string($text['released_at']) ? $text['released_at'] : null)) ?>
  durch <?= Html::e(Format::text(is_string($text['released_by']) ? $text['released_by'] : null)) ?>.
</p>
<?php endif; ?>
<?php else: ?>
<p>Für diesen Text gibt es noch keinen Entwurf. Der erste Text, den Sie speichern, wird Fassung 1.</p>
<?php endif; ?>

<form method="post" action="/admin/rechtstexte/<?= Html::e($slug) ?>" class="karte">
  <?= Csrf::feld() ?>
  <div class="feld">
    <label for="feld-body">Text</label>
    <textarea id="feld-body" name="body" required><?= Html::e($rumpf) ?></textarea>
    <p class="feld__hinweis">Beim Speichern geht der Zustand zurück auf Entwurf. Die Fassung zählt eins hoch.</p>
  </div>
  <div class="knopfreihe">
    <button class="knopf" type="submit">Entwurf speichern</button>
  </div>
</form>

<?php if (is_array($text)): ?>
<form method="post" action="/admin/rechtstexte/<?= Html::e($slug) ?>/freigabe" class="karte">
  <?= Csrf::feld() ?>
  <h2>Freigeben</h2>
  <p>Setzen Sie den Zustand erst, wenn die anwaltliche Prüfung vorliegt.</p>
  <div class="feld">
    <label for="feld-zustand">Neuer Zustand</label>
    <select id="feld-zustand" name="zustand">
<?php foreach ($zustaende as $wert => $name): ?>
      <option value="<?= Html::e($wert) ?>"<?= $wert === $zustand ? ' selected' : '' ?>><?= Html::e($name) ?></option>
<?php endforeach; ?>
    </select>
  </div>
  <?= Ansicht::teil('components/feld', ['name' => 'geprueft_von', 'beschriftung' => 'Prüfende Stelle', 'hinweis' => 'Nur bei Freigabe nötig. Name der Kanzlei oder der Person.']) ?>
  <?= Ansicht::teil('components/feld', ['name' => 'grund', 'beschriftung' => 'Grund', 'pflicht' => true, 'hinweis' => 'Steht später im Protokoll.']) ?>
  <div class="knopfreihe">
    <button class="knopf" type="submit">Zustand setzen</button>
  </div>
</form>
<?php endif; ?>
