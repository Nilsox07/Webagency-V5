<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/**
 * Eine Frage des Bedarfsschecks — Beschriftung, Feld, Hilfetext und Fehlermeldung.
 *
 * **Warum eine Komponente und nicht fünf Ansichten:** Der Bedarfsscheck hat fünf Themen mit
 * vier Feldarten. Fünf Ansichtsdateien wären fünf Stellen, an denen eine Fehlermeldung
 * fehlen kann (§1.3: kein Kopieren von Markup zwischen Seiten).
 *
 * **Der Fehler steht AM Feld** (§9.5), nicht als Sammelmeldung oben. Das erste fehlerhafte
 * Feld bekommt `autofocus` — ohne JavaScript ist das der einzige Weg, den Fokus zu setzen.
 *
 * **`fieldset` nur für Auswahlgruppen.** Ein einzelnes Textfeld in einem `fieldset` liesse
 * die Vorlesehilfe Beschriftung und Legende doppelt sprechen.
 *
 * @var array<string,mixed> $feld     ein Eintrag aus Bedarfsscheck::thema()
 * @var mixed               $wert     die bisherige Antwort
 * @var string|null         $fehler
 * @var bool                $ersterFehler
 */

$name     = (string) $feld['name'];
$art      = (string) ($feld['art'] ?? 'text');
$pflicht  = (bool) ($feld['pflicht'] ?? false);
$hilfe    = $feld['hilfe'] ?? null;
$hilfen   = (array) ($feld['hilfen'] ?? []);
$optionen = (array) ($feld['optionen'] ?? []);
$fehler   = $fehler ?? null;
$ersterFehler = $ersterFehler ?? false;
$gruppe   = $art === 'radio' || $art === 'checkbox';

$kennung       = 'feld-' . $name;
$hilfeKennung  = $hilfe === null ? null : $kennung . '-hilfe';
$fehlerKennung = $fehler === null ? null : $kennung . '-fehler';

/** Für `aria-describedby`: Hilfetext und Fehlermeldung gehören beide zum Feld. */
$beschrieben = trim(($hilfeKennung ?? '') . ' ' . ($fehlerKennung ?? ''));
$beschriebenAttribut = $beschrieben === '' ? '' : ' aria-describedby="' . Html::e($beschrieben) . '"';

/** Der Fokus gehört an das erste fehlerhafte Feld — sonst an keines. */
$fokus = $ersterFehler ? ' autofocus' : '';

$gewaehlt = is_array($wert ?? null) ? $wert : [];
$text     = is_string($wert ?? null) ? $wert : '';

?>
<<?= $gruppe ? 'fieldset' : 'div' ?> class="frage<?= $fehler === null ? '' : ' frage--fehler' ?>">
<?php if ($gruppe): ?>
  <legend><?= Html::e((string) $feld['label']) ?><?= $pflicht ? '' : ' <span class="frage__optional">(optional)</span>' ?></legend>
<?php else: ?>
  <label for="<?= Html::e($kennung) ?>"><?= Html::e((string) $feld['label']) ?><?= $pflicht ? '' : ' <span class="frage__optional">(optional)</span>' ?></label>
<?php endif; ?>
<?php if ($hilfe !== null): ?>
  <p class="frage__hilfe" id="<?= Html::e((string) $hilfeKennung) ?>"><?= Html::e((string) $hilfe) ?></p>
<?php endif; ?>
<?php if ($fehler !== null): ?>
  <p class="frage__fehler" id="<?= Html::e((string) $fehlerKennung) ?>"><?= Html::e($fehler) ?></p>
<?php endif; ?>
<?php if ($gruppe): ?>
  <ul class="wahl">
<?php $lfd = 0; foreach ($optionen as $schluessel => $beschriftung): $lfd++; ?>
    <li>
      <input type="<?= Html::e($art) ?>"
        id="<?= Html::e($kennung . '-' . (string) $lfd) ?>"
        name="<?= Html::e($name . ($art === 'checkbox' ? '[]' : '')) ?>"
        value="<?= Html::e((string) $schluessel) ?>"
        <?= ($art === 'checkbox' ? in_array((string) $schluessel, $gewaehlt, true) : $text === (string) $schluessel)
            ? 'checked' : '' ?><?= $lfd === 1 ? $beschriebenAttribut . $fokus : '' ?>>
      <label for="<?= Html::e($kennung . '-' . (string) $lfd) ?>">
        <?= Html::e((string) $beschriftung) ?>
<?php if (isset($hilfen[$schluessel])): ?>
        <span class="wahl__hilfe"><?= Html::e((string) $hilfen[$schluessel]) ?></span>
<?php endif; ?>
      </label>
    </li>
<?php endforeach; ?>
  </ul>
<?php elseif ($art === 'textarea'): ?>
  <textarea id="<?= Html::e($kennung) ?>" name="<?= Html::e($name) ?>" rows="4"
    <?= $pflicht ? 'required' : '' ?><?= $beschriebenAttribut . $fokus ?>><?= Html::e($text) ?></textarea>
<?php else: ?>
  <input type="text" id="<?= Html::e($kennung) ?>" name="<?= Html::e($name) ?>"
    value="<?= Html::e($text) ?>"
    <?= $pflicht ? 'required' : '' ?><?= $beschriebenAttribut . $fokus ?>>
<?php endif; ?>
</<?= $gruppe ? 'fieldset' : 'div' ?>>
