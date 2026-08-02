<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Html;

/** @var list<string> $fehler */
/** @var list<array{version:string,pfad:string,checksum:string}> $offene */
/** @var array<string,array{checksum:string,applied_at:string}> $eingetragene */

?>
<h1>Das Schema wird angelegt</h1>
<p>Jede Migration läuft einzeln und wird sofort nach dem Erfolg eingetragen.</p>
<p>Bricht eine ab, setzt der nächste Aufruf genau dort fort. Nichts wird doppelt gespielt.</p>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler]) ?>

<div class="karte">
  <h2>Eingespielt: <?= Html::e((string) count($eingetragene)) ?> · offen: <?= Html::e((string) count($offene)) ?></h2>
  <ul class="liste">
<?php foreach ($eingetragene as $version => $eintrag): ?>
    <li><span><?= Html::e($version) ?></span><span class="marke" data-stand="freigegeben">eingespielt</span></li>
<?php endforeach; ?>
<?php foreach ($offene as $datei): ?>
    <li><span><?= Html::e($datei['version']) ?></span><span class="marke">offen</span></li>
<?php endforeach; ?>
  </ul>
</div>

<form method="post" action="/admin/setup/migrationen">
  <?= Csrf::feld() ?>
  <div class="knopfreihe">
    <button class="knopf" type="submit">Migrationen einspielen</button>
  </div>
</form>
