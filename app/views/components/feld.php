<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/**
 * Ein Formularfeld. Beschriftung, Feld und Hinweis gehoeren zusammen und werden deshalb an
 * einer Stelle gebaut — §1.3: kein Kopieren von Markup zwischen Seiten.
 *
 * @var string $name
 * @var string $beschriftung
 */

$art = $art ?? 'text';
$wert = $wert ?? '';
$pflicht = $pflicht ?? false;
$hinweis = $hinweis ?? null;
$autovervollstaendigung = $autovervollstaendigung ?? null;
$kennung = 'feld-' . $name;

?>
<div class="feld">
  <label for="<?= Html::e($kennung) ?>"><?= Html::e($beschriftung) ?></label>
  <input
    id="<?= Html::e($kennung) ?>"
    name="<?= Html::e($name) ?>"
    type="<?= Html::e($art) ?>"
    value="<?= Html::e((string) $wert) ?>"
    <?= $pflicht ? 'required' : '' ?>
    <?= $autovervollstaendigung === null ? '' : 'autocomplete="' . Html::e($autovervollstaendigung) . '"' ?>>
<?php if ($hinweis !== null): ?>
  <p class="feld__hinweis"><?= Html::e($hinweis) ?></p>
<?php endif; ?>
</div>
