<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/** @var string $beschriftung */
/** @var string $rumpf */

?>
<h1><?= Html::e($beschriftung) ?></h1>
<?php foreach (preg_split('/\R{2,}/', trim($rumpf)) ?: [] as $absatz): ?>
<p><?= nl2br(Html::e(trim($absatz))) ?></p>
<?php endforeach; ?>
