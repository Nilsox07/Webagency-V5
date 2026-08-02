<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Html;
use Sartu\Services\Bedarfsscheck;

/**
 * Ein Thema des Bedarfsschecks — Website-Lastenheft §9.2, eine echte Seite je Schritt.
 *
 * Fortschritt als Text: §9.1 verlangt `Thema 1 von 5`, nicht „Frage 1 von 10". Ein
 * Fortschrittsbalken kommt nach §9.5a nur zusätzlich und braucht JavaScript — hier steht
 * deshalb die Zeile.
 *
 * @var int                   $nummer
 * @var array<string,mixed>   $thema
 * @var array<string,mixed>   $werte
 * @var array<string,string>  $fehler
 */

$ersterFehler = array_key_first($fehler);

?>
<p class="vorzeile">Thema <?= Html::e((string) $nummer) ?> von <?= Html::e((string) Bedarfsscheck::SCHRITTE) ?></p>
<h1><?= Html::e((string) $thema['titel']) ?></h1>

<form method="post" action="/briefing/<?= Html::e((string) $nummer) ?>">
  <?= Csrf::feld() ?>
<?php foreach ($thema['felder'] as $feld): ?>
  <?= Ansicht::teil('components/frage', [
      'feld'         => $feld,
      'wert'         => $werte[$feld['name']] ?? null,
      'fehler'       => $fehler[$feld['name']] ?? null,
      'ersterFehler' => $ersterFehler === $feld['name'],
  ]) ?>
<?php endforeach; ?>

  <div class="knopfreihe">
    <button type="submit" class="knopf">
<?php if ($nummer < Bedarfsscheck::SCHRITTE): ?>
      Weiter zu Thema <?= Html::e((string) ($nummer + 1)) ?>
<?php else: ?>
      Empfehlung ansehen
<?php endif; ?>
    </button>
<?php if ($nummer > 1): ?>
    <a class="knopf knopf--ruhig" href="/briefing/<?= Html::e((string) ($nummer - 1)) ?>">Zurück zu Thema <?= Html::e((string) ($nummer - 1)) ?></a>
<?php endif; ?>
  </div>
</form>

<p class="fussnote">Ihre Angaben bleiben 24 Stunden gespeichert. Sie können später fortsetzen.</p>
