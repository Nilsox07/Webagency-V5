<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Data\Customer\KundenOeffnungszeiten;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Html;
use Sartu\Services\Oeffnungszeitendienst;

/**
 * `/portal/inhalte` — Öffnungszeiten, Portal-Lastenheft §8.7. Texte gebunden.
 *
 * **Die Ausnahmeliste hat immer drei leere Zeilen mehr, als gefüllt sind.** §8.7 nennt einen
 * Knopf `Ausnahme hinzufügen` — der bräuchte JavaScript, um eine Zeile nachzuschieben, und
 * §3 Regel 7 verlangt Bedienbarkeit ohne. Leere Zeilen werden beim Speichern verworfen; wer
 * mehr als drei auf einmal braucht, speichert und bekommt die nächsten drei.
 *
 * @var bool $freigegeben
 * @var array<int,array<string,mixed>> $tage
 * @var list<array<string,mixed>> $ausnahmen
 * @var bool $wartet
 * @var list<string> $fehler
 * @var list<string> $hinweise
 */

$leerzeilen = 3;

?>
<h1>Öffnungszeiten</h1>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler, 'hinweise' => $hinweise]) ?>

<?php if (!$freigegeben): ?>
<div class="karte">
  <p><?= Html::e(Oeffnungszeitendienst::VOR_DEM_START) ?></p>
</div>
<?php else: ?>

<?php if ($wartet): ?>
<div class="karte karte--betont">
  <p><?= Html::e(Oeffnungszeitendienst::BANNER_OFFEN) ?></p>
</div>
<?php endif; ?>

<p>Änderungen hier erscheinen nach unserer Prüfung auf Ihrer Website — üblicherweise am
nächsten Werktag.</p>

<form method="post" action="/portal/inhalte">
  <?= Csrf::feld() ?>

  <div class="karte">
    <h2>Woche</h2>
<?php foreach (KundenOeffnungszeiten::TAGE as $nummer => $name): ?>
<?php $tag = $tage[$nummer] ?? null; ?>
    <fieldset class="frage">
      <legend><?= Html::e($name) ?></legend>

      <ul class="wahl">
        <li>
          <input type="checkbox" id="feld-zu-<?= $nummer ?>" name="tage[<?= $nummer ?>][closed]" value="1"
            <?= $tag !== null && (int) $tag['closed'] === 1 ? 'checked' : '' ?>>
          <label for="feld-zu-<?= $nummer ?>">Geschlossen</label>
        </li>
      </ul>

      <div class="feldpaar">
        <div class="feld">
          <label for="feld-von-<?= $nummer ?>">Von</label>
          <input type="time" id="feld-von-<?= $nummer ?>" name="tage[<?= $nummer ?>][open_time]"
            value="<?= Html::e(substr((string) ($tag['open_time'] ?? ''), 0, 5)) ?>">
        </div>
        <div class="feld">
          <label for="feld-bis-<?= $nummer ?>">Bis</label>
          <input type="time" id="feld-bis-<?= $nummer ?>" name="tage[<?= $nummer ?>][close_time]"
            value="<?= Html::e(substr((string) ($tag['close_time'] ?? ''), 0, 5)) ?>">
        </div>
        <div class="feld">
          <label for="feld-hinweis-<?= $nummer ?>">Hinweis</label>
          <input type="text" id="feld-hinweis-<?= $nummer ?>" name="tage[<?= $nummer ?>][note]"
            value="<?= Html::e((string) ($tag['note'] ?? '')) ?>">
        </div>
      </div>
    </fieldset>
<?php endforeach; ?>
  </div>

  <div class="karte">
    <h2>Ausnahmen</h2>
    <p class="leise">Feiertage, Betriebsurlaub, verkürzte Tage. Leere Zeilen werden nicht
    gespeichert.</p>

<?php
$zeilen = $ausnahmen;

for ($i = 0; $i < $leerzeilen; $i++) {
    $zeilen[] = ['date' => '', 'closed' => 1, 'open_time' => '', 'close_time' => '', 'label' => ''];
}
?>
<?php foreach ($zeilen as $nummer => $ausnahme): ?>
    <fieldset class="frage">
      <legend>Ausnahme <?= (int) $nummer + 1 ?></legend>

      <div class="feldpaar">
        <div class="feld">
          <label for="feld-ausnahme-datum-<?= (int) $nummer ?>">Datum</label>
          <input type="date" id="feld-ausnahme-datum-<?= (int) $nummer ?>"
            name="ausnahmen[<?= (int) $nummer ?>][date]"
            value="<?= Html::e((string) $ausnahme['date']) ?>">
        </div>
        <div class="feld">
          <label for="feld-ausnahme-name-<?= (int) $nummer ?>">Bezeichnung</label>
          <input type="text" id="feld-ausnahme-name-<?= (int) $nummer ?>"
            name="ausnahmen[<?= (int) $nummer ?>][label]"
            value="<?= Html::e((string) $ausnahme['label']) ?>" placeholder="Feiertag">
        </div>
      </div>

      <ul class="wahl">
        <li>
          <input type="checkbox" id="feld-ausnahme-zu-<?= (int) $nummer ?>"
            name="ausnahmen[<?= (int) $nummer ?>][closed]" value="1"
            <?= (int) $ausnahme['closed'] === 1 ? 'checked' : '' ?>>
          <label for="feld-ausnahme-zu-<?= (int) $nummer ?>">Geschlossen</label>
        </li>
      </ul>

      <div class="feldpaar">
        <div class="feld">
          <label for="feld-ausnahme-von-<?= (int) $nummer ?>">Von</label>
          <input type="time" id="feld-ausnahme-von-<?= (int) $nummer ?>"
            name="ausnahmen[<?= (int) $nummer ?>][open_time]"
            value="<?= Html::e(substr((string) $ausnahme['open_time'], 0, 5)) ?>">
        </div>
        <div class="feld">
          <label for="feld-ausnahme-bis-<?= (int) $nummer ?>">Bis</label>
          <input type="time" id="feld-ausnahme-bis-<?= (int) $nummer ?>"
            name="ausnahmen[<?= (int) $nummer ?>][close_time]"
            value="<?= Html::e(substr((string) $ausnahme['close_time'], 0, 5)) ?>">
        </div>
      </div>
    </fieldset>
<?php endforeach; ?>
    <p class="leise">Eine Ausnahme entfernen Sie, indem Sie ihr Datum löschen.</p>
  </div>

  <button type="submit" class="knopf">Änderungen einreichen</button>
</form>

<div class="karte">
  <p class="leise">Layout, Seitenstruktur, Adressen und Texte ändern wir für Sie — schreiben
  Sie uns dazu einfach über <a href="/portal/hilfe">Hilfe</a>.</p>
</div>
<?php endif; ?>
