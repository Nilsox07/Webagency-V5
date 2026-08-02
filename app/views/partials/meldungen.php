<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/**
 * Fehler und Hinweise.
 *
 * Es gibt eine Akzentfarbe. Kein Rot fuer Fehler, kein Gruen fuer Erfolg
 * (SARTU_DESIGNSYSTEM.md) — unterschieden wird ueber Flaeche, Kante und Ueberschrift.
 *
 * @var list<string> $fehler
 * @var list<string> $hinweise
 */

$fehler = $fehler ?? [];
$hinweise = $hinweise ?? [];

?>
<?php if ($fehler !== []): ?>
<div class="meldung" role="alert">
  <p><strong>Das hat noch nicht geklappt.</strong></p>
  <ul>
<?php foreach ($fehler as $eintrag): ?>
    <li><?= Html::e($eintrag) ?></li>
<?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>
<?php if ($hinweise !== []): ?>
<div class="meldung meldung--hinweis">
  <ul>
<?php foreach ($hinweise as $eintrag): ?>
    <li><?= Html::e($eintrag) ?></li>
<?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>
