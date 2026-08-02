<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/** @var string $titel */
/** @var string $meldung */
/** @var string|null $kennung */

?>
<h1><?= Html::e($titel) ?></h1>
<p><?= Html::e($meldung) ?></p>
<?php if (isset($kennung) && $kennung !== null): ?>
<p class="leise">Kennung für die Rückfrage: <?= Html::e($kennung) ?></p>
<?php endif; ?>
