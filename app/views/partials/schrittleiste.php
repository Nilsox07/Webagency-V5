<?php

declare(strict_types=1);

use Sartu\Helpers\Html;
use Sartu\Services\Ersteinrichtung;

/** @var int $schritt */

?>
<p class="vorzeile">Einrichtung · Schritt <?= Html::e((string) $schritt) ?> von 8</p>
<ol class="schritte">
<?php foreach (Ersteinrichtung::SCHRITTE as $nummer => $name): ?>
  <li data-stand="<?= $nummer < $schritt ? 'erledigt' : ($nummer === $schritt ? 'hier' : 'offen') ?>">
    <?= Html::e((string) $nummer) ?> <?= Html::e($name) ?>
  </li>
<?php endforeach; ?>
</ol>
