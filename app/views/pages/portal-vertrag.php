<?php

declare(strict_types=1);

use Sartu\Data\RechtstexteSpeicher;
use Sartu\Helpers\Html;

/**
 * `/portal/vertrag` — Portal-Lastenheft §8.6 und §15.2.
 *
 * „Das Programm zeigt den Vertrag an […] **Es formuliert ihn nicht.**" Solange kein Text
 * freigegeben ist, steht hier der Leerzustand und kein Platzhaltertext.
 *
 * @var list<array<string,mixed>> $texte
 */

?>
<h1>Vertrag</h1>

<?php if ($texte === []): ?>
<div class="karte">
  <p>Sobald der Auftragsverarbeitungsvertrag und die technischen Maßnahmen vorliegen, finden
  Sie sie hier. Wir sagen Ihnen Bescheid, wenn es so weit ist.</p>
</div>
<?php else: ?>
<?php foreach ($texte as $text): ?>
<div class="karte">
  <h2><?= Html::e(RechtstexteSpeicher::beschriftung((string) $text['slug'])) ?></h2>
  <div class="rechtstext"><?= nl2br(Html::e((string) $text['body'])) ?></div>
</div>
<?php endforeach; ?>
<?php endif; ?>
