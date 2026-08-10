<?php

declare(strict_types=1);

use Sartu\Helpers\Csrf;
use Sartu\Helpers\Html;
use Sartu\Services\Zahlungsschluessel;

/**
 * `/admin/ersteinrichtung`.
 *
 * Oben, was die Veröffentlichung anhält. Darunter, was sie nicht anhält, aber fehlt.
 * Zuletzt die beiden Einstellungen, die es sonst nirgends gibt.
 *
 * @var list<string> $hindernisse
 * @var list<string> $weiterePunkte
 * @var bool $logo
 * @var string $feld welches Schlüsselfeld in dieser Umgebung gilt
 * @var string|null $spurTest
 * @var string|null $spurLive
 * @var list<string> $fehler
 * @var list<string> $hinweise
 */

$istTest = $feld === Zahlungsschluessel::FELD_TEST;

?>

<?php foreach ($fehler as $meldung): ?>
<p class="hinweis hinweis--wichtig"><?= Html::e($meldung) ?></p>
<?php endforeach; ?>
<?php foreach ($hinweise as $meldung): ?>
<p class="hinweis"><?= Html::e($meldung) ?></p>
<?php endforeach; ?>

<?php if ($hindernisse === []): ?>
<div class="meldung meldung--hinweis">
  <p><strong>Der Start ist frei.</strong> Betreiberdaten und Rechtstexte sind vollständig.</p>
</div>
<?php else: ?>
<div class="meldung">
  <p><strong>Das hält die Veröffentlichung an.</strong> Solange hier etwas steht, geht nichts
  mit Platzhaltern nach außen.</p>
  <ul>
<?php foreach ($hindernisse as $hindernis): ?>
    <li><?= Html::e($hindernis) ?></li>
<?php endforeach; ?>
  </ul>
  <p class="leise"><a href="/admin/einstellungen/betrieb">Betreiberdaten pflegen</a> ·
  <a href="/admin/rechtstexte">Rechtstexte verwalten</a></p>
</div>
<?php endif; ?>

<?php if ($weiterePunkte !== []): ?>
<div class="karte">
  <h2>Weitere Punkte</h2>
  <p>Diese halten die Veröffentlichung <strong>nicht</strong> an. Sie fehlen trotzdem.</p>
  <ul class="liste">
<?php foreach ($weiterePunkte as $punkt): ?>
    <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<div class="karte">
  <h2>Zahlungsschlüssel</h2>
  <p>Er wird verschlüsselt gespeichert und danach nie wieder angezeigt — Sie sehen hier
  höchstens die letzten vier Zeichen. Welcher der beiden gilt, entscheidet die Umgebung des
  Servers, nicht ein Schalter auf dieser Seite.</p>

  <ul class="pruefliste">
    <li>
      <span>Testschlüssel<?= $istTest ? ' (gilt hier)' : '' ?></span>
      <span><?= $spurTest === null ? 'Noch nicht hinterlegt' : Html::e($spurTest) ?></span>
    </li>
    <li>
      <span>Produktivschlüssel<?= $istTest ? '' : ' (gilt hier)' ?></span>
      <span><?= $spurLive === null ? 'Noch nicht hinterlegt' : Html::e($spurLive) ?></span>
    </li>
  </ul>

  <form method="post" action="/admin/ersteinrichtung/zahlungsschluessel">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-schluesselart">Welchen Schlüssel hinterlegen Sie?</label>
      <select id="feld-schluesselart" name="feld">
        <option value="<?= Html::e(Zahlungsschluessel::FELD_TEST) ?>"<?= $istTest ? ' selected' : '' ?>>Testschlüssel</option>
        <option value="<?= Html::e(Zahlungsschluessel::FELD_LIVE) ?>"<?= $istTest ? '' : ' selected' ?>>Produktivschlüssel</option>
      </select>
    </div>
    <div class="feld">
      <label for="feld-schluessel">Schlüssel</label>
      <input type="password" id="feld-schluessel" name="schluessel" value="" required
        autocomplete="off" minlength="<?= (int) Zahlungsschluessel::MINDESTLAENGE ?>">
      <p class="leise">Sie finden ihn im Konto Ihres Zahlungsdienstes. Er beginnt mit
      <code>test_</code> oder <code>live_</code>.</p>
    </div>
    <button type="submit" class="knopf">Schlüssel hinterlegen</button>
  </form>

<?php if ($spurTest !== null || $spurLive !== null): ?>
  <form method="post" action="/admin/ersteinrichtung/zahlungsschluessel-entfernen">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-entfernen">Schlüssel entfernen</label>
      <select id="feld-entfernen" name="feld">
        <option value="<?= Html::e(Zahlungsschluessel::FELD_TEST) ?>">Testschlüssel</option>
        <option value="<?= Html::e(Zahlungsschluessel::FELD_LIVE) ?>">Produktivschlüssel</option>
      </select>
    </div>
    <button type="submit" class="knopf knopf--ruhig">Entfernen</button>
  </form>
<?php endif; ?>
</div>

<div class="karte">
  <h2>Logo</h2>
<?php if ($logo): ?>
  <p>Das Logo liegt im ausgelieferten Verzeichnis. Rechnungsbelege tragen es.</p>
<?php else: ?>
  <p>Das Logo fehlt unter <code>public/assets/bild/</code>. Es gehört zur Marke und wird
  nicht hochgeladen, sondern aus <code>design/</code> mit ausgeliefert — bitte prüfen Sie die
  Veröffentlichung des Verzeichnisses.</p>
<?php endif; ?>
</div>
