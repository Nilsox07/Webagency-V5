<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;

/**
 * Aufgabendetail — Portal-Lastenheft §8.3, je nach `kind` eine andere Eingabe.
 *
 * Der Sonderfall `freigabe` ist keine gewoehnliche Rueckmeldung, sondern eine
 * protokollierte Erklaerung. Er zeigt vorher, WAS freigegeben wird — sonst gibt der Kunde
 * etwas frei, das er nicht sieht.
 *
 * @var array<string,mixed> $aufgabe
 * @var list<array<string,mixed>> $dateien
 * @var list<string> $fehler
 * @var int $offenePflicht
 * @var list<array<string,mixed>> $bisher
 * @var array<string,mixed>|null $freigabe
 * @var array<string,mixed>|null $angebot
 * @var array<string,mixed>|null $projekt
 */

$art = (string) $aufgabe['kind'];
$erledigt = (string) $aufgabe['status'] === 'erledigt';
$id = (string) $aufgabe['id'];

?>
<p class="vorzeile"><a href="/portal/aufgaben">Zurück zu Ihren Aufgaben</a></p>

<?php if ($art === 'freigabe'): ?>
<h1>Fakten und Umfang final freigeben</h1>
<p class="lead">Bitte prüfen Sie Ihre Angaben ein letztes Mal. Danach beginnen wir mit der
Produktion. Spätere Änderungen an Fakten oder Umfang sind dann nicht mehr ohne Weiteres
möglich.</p>
<?php else: ?>
<h1><?= Html::e((string) $aufgabe['title']) ?></h1>
<?php if ($aufgabe['description'] !== null): ?>
<p class="lead"><?= nl2br(Html::e((string) $aufgabe['description'])) ?></p>
<?php endif; ?>
<?php endif; ?>

<?php if ($aufgabe['why_needed'] !== null && trim((string) $aufgabe['why_needed']) !== ''): ?>
<p class="leise">Warum wir das brauchen: <?= Html::e((string) $aufgabe['why_needed']) ?></p>
<?php endif; ?>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler, 'hinweise' => []]) ?>

<?php if ($freigabe !== null): ?>
<div class="karte karte--betont">
  <p>Freigegeben am <?= Html::e(Format::datum((string) $freigabe['granted_at'])) ?> durch
  <?= Html::e((string) $freigabe['granted_name']) ?>.</p>
<?php if ($angebot !== null): ?>
  <p>Fertigstellung voraussichtlich in <?= Html::e((string) $angebot['delivery_days_min']) ?>–<?= Html::e((string) $angebot['delivery_days_max']) ?> Werktagen.</p>
<?php endif; ?>
</div>
<?php elseif ($erledigt): ?>
<div class="karte karte--betont">
  <p>Erledigt am <?= Html::e(Format::datum((string) $aufgabe['completed_at'])) ?>.</p>
<?php if ($aufgabe['answer_text'] !== null): ?>
  <p><?= nl2br(Html::e((string) $aufgabe['answer_text'])) ?></p>
<?php endif; ?>
</div>

<?php elseif ($art === 'freigabe'): ?>
<div class="karte">
  <h2>Das geben Sie frei</h2>
<?php if ($bisher === []): ?>
  <p>Zu diesem Projekt sind noch keine Angaben erledigt.</p>
<?php else: ?>
  <ul class="pruefliste">
<?php foreach ($bisher as $eintrag): ?>
    <li><span><?= Html::e((string) $eintrag['title']) ?></span><span><?= Html::e(Format::text($eintrag['answer_text'] === null ? null : (string) $eintrag['answer_text'])) ?></span></li>
<?php endforeach; ?>
  </ul>
<?php endif; ?>
<?php if ($angebot !== null): ?>
  <p>Vereinbarter Umfang: <?= Html::e((string) $angebot['scope_pages']) ?> Seiten,
  <?= Html::e((string) $angebot['included_feedback_rounds']) ?> Korrekturrunden.</p>
<?php endif; ?>
</div>

<?php if ($offenePflicht > 0): ?>
<div class="meldung" role="status">
  <p>Bitte schließen Sie zuerst die noch offenen Aufgaben ab.</p>
  <p><a href="/portal/aufgaben">Zu Ihren Aufgaben</a></p>
</div>
<?php else: ?>
<div class="karte">
  <form method="post" action="/portal/aufgaben/<?= Html::e($id) ?>/abschliessen">
    <?= Csrf::feld() ?>
    <p class="haken">
      <input type="checkbox" id="feld-bestaetigung" name="bestaetigung" value="1">
      <label for="feld-bestaetigung">Die Angaben sind vollständig und richtig. Der Umfang ist
      so vereinbart.</label>
    </p>
    <div class="feld">
      <label for="feld-granted_name">Ihr Name</label>
      <input type="text" id="feld-granted_name" name="granted_name" value="" required>
    </div>
    <button type="submit" class="knopf">Verbindlich freigeben</button>
  </form>
</div>
<?php endif; ?>

<?php else: ?>
<?php if ($art === 'upload'): ?>
<div class="karte">
  <h2>Dateien</h2>
<?php if ($dateien === []): ?>
  <p>Noch keine Datei hochgeladen.</p>
<?php else: ?>
  <ul class="liste">
<?php foreach ($dateien as $datei): ?>
    <li>
      <span><a href="/portal/dateien/<?= Html::e((string) $datei['id']) ?>"><?= Html::e((string) $datei['original_name']) ?></a></span>
      <span class="leise"><?= Html::e(number_format((int) $datei['size_bytes'] / 1048576, 1, ',', '.')) ?> MB</span>
    </li>
<?php endforeach; ?>
  </ul>
<?php endif; ?>

  <form method="post" action="/portal/aufgaben/<?= Html::e($id) ?>/datei" enctype="multipart/form-data">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-datei">Datei auswählen</label>
      <input type="file" id="feld-datei" name="datei" required>
      <p class="feld__hinweis">Bilder, PDF, Word-Dateien und ZIP-Archive. Höchstens 20 MB je
      Datei, 10 Dateien je Aufgabe.</p>
    </div>
    <p class="haken">
      <input type="checkbox" id="feld-rights_confirmed" name="rights_confirmed" value="1">
      <label for="feld-rights_confirmed">Ich habe die Rechte an diesen Dateien und darf sie
      für meine Website verwenden.</label>
    </p>
    <button type="submit" class="knopf knopf--ruhig">Datei hochladen</button>
  </form>
</div>
<?php endif; ?>

<div class="karte">
  <form method="post" action="/portal/aufgaben/<?= Html::e($id) ?>/abschliessen">
    <?= Csrf::feld() ?>
<?php if ($art === 'angabe'): ?>
    <div class="feld">
      <label for="feld-answer_text">Ihre Antwort</label>
      <textarea id="feld-answer_text" name="answer_text" required></textarea>
    </div>
<?php elseif ($art === 'bestaetigung'): ?>
    <div class="feld">
      <label for="feld-answer_text">Korrektur <span class="frage__optional">(optional)</span></label>
      <textarea id="feld-answer_text" name="answer_text"></textarea>
      <p class="feld__hinweis">Stimmt alles? Dann lassen Sie das Feld leer.</p>
    </div>
<?php endif; ?>
    <div class="knopfreihe">
      <button type="submit" class="knopf">Aufgabe abschließen</button>
      <a class="knopf knopf--ruhig" href="/portal/aufgaben">Später</a>
    </div>
  </form>
</div>
<?php endif; ?>
