<?php

declare(strict_types=1);

use Sartu\Helpers\Csrf;
use Sartu\Helpers\Html;

/** @var list<array{punkt:string,erfuellt:bool,hinweis:string}> $pruefungen */
/** @var bool $bereit */

?>
<h1>Wir prüfen zuerst den Server</h1>
<p>Fehlt hier etwas, scheitert die Einrichtung später an einer schwerer lesbaren Stelle.</p>

<div class="karte">
  <ul class="pruefliste">
<?php foreach ($pruefungen as $pruefung): ?>
    <li>
      <span><?= Html::e($pruefung['punkt']) ?></span>
      <span class="marke" data-stand="<?= $pruefung['erfuellt'] ? 'freigegeben' : 'offen' ?>">
        <?= $pruefung['erfuellt'] ? 'in Ordnung' : 'fehlt' ?>
      </span>
    </li>
<?php endforeach; ?>
  </ul>
</div>

<?php foreach ($pruefungen as $pruefung): ?>
<?php if (!$pruefung['erfuellt']): ?>
<p class="leise"><?= Html::e($pruefung['punkt']) ?>: <?= Html::e($pruefung['hinweis']) ?></p>
<?php endif; ?>
<?php endforeach; ?>

<?php if ($bereit): ?>
<form method="post" action="/admin/setup">
  <?= Csrf::feld() ?>
  <div class="knopfreihe">
    <button class="knopf" type="submit">Zur Datenbank weitergehen</button>
  </div>
</form>
<?php else: ?>
<p>Beheben Sie die offenen Punkte und laden Sie diese Seite neu.</p>
<?php endif; ?>
