<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;

/**
 * `/portal/hilfe` — Portal-Lastenheft §8.9.
 *
 * @var list<array<string,mixed>> $nachrichten
 * @var list<string> $fehler
 * @var list<string> $hinweise
 */

?>
<h1>Hilfe</h1>
<p class="lead">Schreiben Sie uns, was unklar ist. Wir antworten schriftlich — meist am
selben oder nächsten Werktag.</p>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler, 'hinweise' => $hinweise]) ?>

<div class="karte">
  <form method="post" action="/portal/hilfe">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-body">Ihre Nachricht</label>
      <textarea id="feld-body" name="body" required></textarea>
    </div>
    <button type="submit" class="knopf">Nachricht senden</button>
  </form>
</div>

<?php if ($nachrichten !== []): ?>
<div class="karte">
  <h2>Ihre bisherigen Nachrichten</h2>
  <ul class="liste">
<?php foreach ($nachrichten as $nachricht): ?>
    <li>
      <span>
        <span class="leise"><?= Html::e(Format::datumZeit((string) $nachricht['created_at'])) ?></span>
        <?= nl2br(Html::e((string) $nachricht['body'])) ?>
<?php if ($nachricht['answer_text'] !== null): ?>
        <span class="antwort"><strong>Unsere Antwort:</strong> <?= nl2br(Html::e((string) $nachricht['answer_text'])) ?></span>
<?php endif; ?>
      </span>
      <span><?= $nachricht['answered_at'] === null ? 'Offen' : 'Beantwortet' ?></span>
    </li>
<?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>
