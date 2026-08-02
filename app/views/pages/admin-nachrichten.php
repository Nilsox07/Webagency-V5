<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;

/**
 * Support-Nachrichten mit Antwortfeld — Portal-Lastenheft §9.1.
 *
 * Unbeantwortete stehen oben; beantwortete bleiben stehen und zeigen ihre Antwort. Eine
 * Antwort, die man nach dem Absenden nicht mehr nachlesen kann, ist im Streitfall keine.
 *
 * Jede Antwort ist ein gewöhnliches Formular mit CSRF-Feld — die Seite funktioniert mit
 * abgeschaltetem JavaScript (§3 Regel 7).
 *
 * @var list<array<string,mixed>> $nachrichten
 * @var list<string> $fehler
 * @var list<string> $hinweise
 */

?>
<h1>Nachrichten</h1>
<p>Der Kunde schreibt in seinem Bereich unter „Hilfe". Die Antwort geht ihm zusätzlich
per E-Mail zu.</p>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler, 'hinweise' => $hinweise]) ?>

<?php if ($nachrichten === []): ?>
<div class="karte">
  <p>Es liegt keine Nachricht vor.</p>
</div>
<?php else: ?>
<?php foreach ($nachrichten as $nachricht): ?>
<?php $beantwortet = $nachricht['answered_at'] !== null; ?>
<div class="karte">
  <p class="vorzeile">
    <?= Html::e(Format::text((string) $nachricht['legal_name'])) ?>
    · <?= Html::e(Format::datumZeit((string) $nachricht['created_at'])) ?>
<?php if (($nachricht['project_title'] ?? null) !== null): ?>
    · <?= Html::e(Format::text((string) $nachricht['project_title'])) ?>
<?php endif; ?>
  </p>

  <p><?= nl2br(Html::e((string) $nachricht['body'])) ?></p>

<?php if ($beantwortet): ?>
  <p class="vorzeile">Beantwortet am <?= Html::e(Format::datumZeit((string) $nachricht['answered_at'])) ?></p>
  <p class="leise"><?= nl2br(Html::e(Format::text($nachricht['answer_text'] === null ? null : (string) $nachricht['answer_text']))) ?></p>
<?php else: ?>
  <form method="post" action="/admin/nachrichten/<?= Html::e((string) $nachricht['id']) ?>/antwort">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-antwort-<?= Html::e((string) $nachricht['id']) ?>">Ihre Antwort</label>
      <textarea id="feld-antwort-<?= Html::e((string) $nachricht['id']) ?>" name="answer_text" required></textarea>
    </div>
    <button type="submit" class="knopf">Antwort senden</button>
  </form>
<?php endif; ?>
</div>
<?php endforeach; ?>
<?php endif; ?>
