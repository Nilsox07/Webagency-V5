<?php

declare(strict_types=1);

use Sartu\Helpers\Format;
use Sartu\Helpers\Html;

/**
 * `/portal/aufgaben` — Portal-Lastenheft §8.3. Texte gebunden.
 *
 * @var list<array<string,mixed>> $aufgaben
 */

$offen = array_values(array_filter($aufgaben, static fn (array $a) => (string) $a['status'] === 'offen'));

?>
<h1>Ihre Aufgaben</h1>

<?php if ($aufgaben === []): ?>
<div class="karte">
  <p>Aktuell nichts zu tun. Sobald wir etwas von Ihnen brauchen, erscheint es hier — Sie
  bekommen zusätzlich eine E-Mail.</p>
</div>
<?php else: ?>
<?php if ($offen !== []): ?>
<p class="lead">Wir haben vorausgefüllt, was wir schon über Ihr Unternehmen wissen. Sie
bestätigen es oder korrigieren es. Sie müssen nicht alles auf einmal machen — Ihr Stand wird
gespeichert.</p>
<?php endif; ?>

<div class="karte">
  <ul class="liste">
<?php foreach ($aufgaben as $aufgabe): ?>
    <li<?= (string) $aufgabe['status'] === 'erledigt' ? ' class="liste__ruhig"' : '' ?>>
      <span>
        <a href="/portal/aufgaben/<?= Html::e((string) $aufgabe['id']) ?>"><?= Html::e((string) $aufgabe['title']) ?></a>
        <span class="leise"><?= Html::e(Format::text($aufgabe['description'] === null ? null : (string) $aufgabe['description'])) ?></span>
      </span>
      <span><?= (string) $aufgabe['status'] === 'erledigt' ? 'Erledigt' : 'Offen' ?></span>
    </li>
<?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>
