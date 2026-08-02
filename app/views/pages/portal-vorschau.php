<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Projektstatus;
use Sartu\Services\Vorschaudienst;

/**
 * `/portal/vorschau` — Portal-Lastenheft §8.4. Texte gebunden.
 *
 * Die Rundenanzeige steht IMMER, sobald eine Runde offen ist — §5.6a: „Das Portal muss sie
 * sichtbar machen, sonst wird Feedback endlos." Blockiert wird nichts.
 *
 * @var array<string,mixed>|null $projekt
 * @var array<string,mixed>|null $runde
 * @var list<array<string,mixed>> $runden
 * @var list<array<string,mixed>> $rueckmeldungen
 * @var array<string,mixed>|null $abnahme
 * @var list<string> $fehler
 */

$enthaltene = $projekt === null ? 0 : (int) $projekt['included_feedback_rounds'];
$vorschauAdresse = $projekt['preview_url'] ?? null;

?>
<h1>Vorschau und Freigabe</h1>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler, 'hinweise' => []]) ?>

<?php if ($projekt === null || !is_string($vorschauAdresse) || $vorschauAdresse === ''): ?>
<div class="karte">
  <p>Sobald die erste Fassung Ihrer Website bereitsteht, finden Sie hier die Vorschau und
  können Rückmeldung geben. Wir sagen Ihnen Bescheid.</p>
</div>
<?php else: ?>
<div class="karte">
  <p class="lead">So sieht Ihre Website aktuell aus. Sehen Sie sich in Ruhe alles an und
  sammeln Sie Ihre Rückmeldungen — es ist einfacher für beide Seiten, wenn alles gebündelt
  kommt.</p>
  <p class="knopfreihe">
    <a class="knopf" href="<?= Html::e($vorschauAdresse) ?>" target="_blank" rel="noopener noreferrer">Vorschau öffnen</a>
  </p>
  <p class="leise">Die Vorschau ist noch nicht öffentlich und für Suchmaschinen gesperrt.</p>
</div>

<?php if ($runde !== null): ?>
<div class="karte">
  <p class="vorzeile">Korrekturrunde <?= Html::e((string) $runde['number']) ?> von <?= Html::e((string) $enthaltene) ?></p>

<?php if ((int) $runde['included'] === 0): ?>
  <div class="meldung" role="status">
    <p><?= Html::e(Vorschaudienst::hinweisZusatzrunde($enthaltene)) ?></p>
  </div>
<?php endif; ?>

<?php if ((string) $runde['status'] === 'offen'): ?>
  <form method="post" action="/portal/vorschau/rueckmeldung">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-body">Ihre Rückmeldung</label>
      <textarea id="feld-body" name="body" required></textarea>
    </div>
    <div class="feld">
      <label for="feld-page_hint">Betrifft welche Seite? <span class="frage__optional">(optional)</span></label>
      <input type="text" id="feld-page_hint" name="page_hint" value="">
    </div>
    <button type="submit" class="knopf knopf--ruhig">Rückmeldung senden</button>
    <p class="feld__hinweis">Sie können mehrere Rückmeldungen senden. Wir bearbeiten sie gebündelt.</p>
  </form>
<?php else: ?>
  <p>Eingereicht am <?= Html::e(Format::datum((string) $runde['submitted_at'])) ?>. Wir melden
  uns, sobald die neue Fassung bereitsteht.</p>
<?php endif; ?>

<?php if ($rueckmeldungen !== []): ?>
  <h2>Ihre Rückmeldungen in dieser Runde</h2>
  <ul class="liste">
<?php foreach ($rueckmeldungen as $eintrag): ?>
    <li>
      <span>
        <?= nl2br(Html::e((string) $eintrag['body'])) ?>
<?php if ($eintrag['page_hint'] !== null): ?>
        <span class="leise">Betrifft: <?= Html::e((string) $eintrag['page_hint']) ?></span>
<?php endif; ?>
<?php if ($eintrag['answer_text'] !== null): ?>
        <span class="antwort"><strong>Unsere Antwort:</strong> <?= nl2br(Html::e((string) $eintrag['answer_text'])) ?></span>
<?php endif; ?>
      </span>
      <span><?= Html::e(match ((string) $eintrag['status']) {
          'beantwortet' => 'Beantwortet',
          'erledigt'    => 'Erledigt',
          default       => 'Offen',
      }) ?></span>
    </li>
<?php endforeach; ?>
  </ul>
<?php endif; ?>

<?php if ((string) $runde['status'] === 'offen'): ?>
<?php if ($rueckmeldungen === []): ?>
  <p class="leise">Bitte geben Sie zuerst eine Rückmeldung ein.</p>
<?php else: ?>
  <form method="post" action="/portal/vorschau/einreichen">
    <?= Csrf::feld() ?>
    <p>Danach können Sie in dieser Runde nichts mehr ergänzen. Wir arbeiten alles gebündelt
    ein und melden uns mit der neuen Fassung. Möchten Sie einreichen?</p>
    <div class="knopfreihe">
      <button type="submit" class="knopf">Ja, einreichen</button>
      <a class="knopf knopf--ruhig" href="/portal/vorschau">Noch nicht</a>
    </div>
  </form>
<?php endif; ?>
<?php endif; ?>
</div>
<?php endif; ?>

<?php if ($abnahme !== null): ?>
<div class="karte karte--betont">
  <h2>Abgenommen</h2>
  <p>Abgenommen am <?= Html::e(Format::datum((string) $abnahme['granted_at'])) ?> durch
  <?= Html::e((string) $abnahme['granted_name']) ?>.</p>
</div>
<?php elseif ($projekt !== null && (string) $projekt['status'] === Projektstatus::ABNAHME): ?>
<div class="karte karte--betont">
  <h2>Website abnehmen</h2>
  <p>Mit der Abnahme bestätigen Sie, dass die Website dem vereinbarten Umfang entspricht.
  Danach stellen wir die Schlussrechnung und bereiten den Start vor.</p>
  <form method="post" action="/portal/vorschau/abnehmen">
    <?= Csrf::feld() ?>
    <p class="haken">
      <input type="checkbox" id="feld-abnahme" name="bestaetigung" value="1">
      <label for="feld-abnahme">Die Website entspricht dem vereinbarten Umfang.</label>
    </p>
    <div class="feld">
      <label for="feld-granted_name">Ihr Name</label>
      <input type="text" id="feld-granted_name" name="granted_name" value="" required>
    </div>
    <button type="submit" class="knopf">Website abnehmen</button>
  </form>
</div>
<?php endif; ?>
<?php endif; ?>
