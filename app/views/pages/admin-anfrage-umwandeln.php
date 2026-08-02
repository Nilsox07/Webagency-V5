<?php

declare(strict_types=1);

use Sartu\Helpers\Csrf;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Preise;

/**
 * Der Bestaetigungsschritt vor der Umwandlung — Portal-Lastenheft §4b.5.
 *
 * „**Bestaetigungsdialog vorher**, weil dabei ein Zugang entsteht." Die Seite sagt, was
 * entsteht, bevor sie fragt — ein „Sind Sie sicher?" nennt die Auswirkung nicht.
 *
 * @var array<string,mixed> $anfrage
 */

$empfohlen = $anfrage['recommended_package'] === null ? '' : (string) $anfrage['recommended_package'];

?>
<p class="vorzeile"><a href="/admin/anfragen/<?= Html::e((string) $anfrage['id']) ?>">Zurück zur Anfrage</a></p>
<h1>Anfrage von <?= Html::e(Format::text((string) $anfrage['company'])) ?> umwandeln</h1>

<div class="karte">
  <h2>Was dabei entsteht</h2>
  <ul class="pruefliste">
    <li><span>Organisation</span><span><?= Html::e(Format::text((string) $anfrage['company'])) ?></span></li>
    <li><span>Zugang für</span><span><?= Html::e(trim((string) $anfrage['first_name'] . ' ' . (string) $anfrage['last_name'])) ?>, <?= Html::e((string) $anfrage['email']) ?></span></li>
    <li><span>Projekt im Zustand</span><span>Angebot offen</span></li>
    <li><span>Einladung per E-Mail</span><span>wird verschickt</span></li>
  </ul>
  <p class="leise">Der Kunde meldet sich ohne Passwort an. Er fordert sich den Anmeldelink
  selbst an.</p>
</div>

<div class="karte">
  <h2>Mit welchem Umfang startet das Projekt?</h2>
<?php if ($empfohlen === 'unklar'): ?>
  <p>Der Bedarfsscheck kam zu keiner Empfehlung. Wählen Sie den Umfang, mit dem Sie das
  Projekt anlegen wollen.</p>
<?php elseif ($empfohlen !== ''): ?>
  <p>Empfohlen wurde <strong><?= Html::e(Preise::name($empfohlen)) ?></strong>. Sie können
  abweichen.</p>
<?php endif; ?>

  <form method="post" action="/admin/anfragen/<?= Html::e((string) $anfrage['id']) ?>/umwandeln">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-paket">Umfang</label>
      <select id="feld-paket" name="paket" required>
<?php foreach (Preise::tabelle() as $schluessel => $zeile): ?>
        <option value="<?= Html::e($schluessel) ?>" <?= $empfohlen === $schluessel ? 'selected' : '' ?>>
          <?= Html::e((string) $zeile['name']) ?> — <?= Html::e((string) $zeile['seiten']) ?>
        </option>
<?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="knopf">In Kunde und Projekt umwandeln</button>
  </form>
</div>
