<?php

declare(strict_types=1);

use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Portal\PortalSteuerung;
use Sartu\Services\Preise;
use Sartu\Services\Projektstatus;

/**
 * Das Cockpit — Portal-Lastenheft §8.1.
 *
 * Block 1 „Naechster Schritt" steht ganz oben und hervorgehoben. Block 2 zeigt den
 * Projektstand mit der Fortschrittsanzeige ueber die sieben Stationen; welcher Zustand auf
 * welcher Station steht, ist in §8.1 verbindlich festgelegt und steht in `Projektstatus`.
 *
 * Block 3 (offene Punkte) und Block 4 (letzte Aktivitaet) brauchen `invoices`, `tasks` und
 * `approvals` — die entstehen in A2. Sie werden hier nicht angedeutet (§0.3b).
 *
 * @var array<string,mixed>|null $projekt
 * @var array<string,mixed>|null $angebot
 * @var array{text:string,ziel:?string,knopf:?string} $naechsterSchritt
 */

?>
<h1>Übersicht</h1>

<div class="karte karte--betont">
  <p class="vorzeile">Nächster Schritt</p>
  <p class="lead"><?= Html::e($naechsterSchritt['text']) ?></p>
<?php if ($naechsterSchritt['ziel'] !== null): ?>
  <p class="knopfreihe"><a class="knopf" href="<?= Html::e($naechsterSchritt['ziel']) ?>"><?= Html::e((string) $naechsterSchritt['knopf']) ?></a></p>
<?php endif; ?>
</div>

<?php if ($projekt === null): ?>
<div class="karte">
  <h2>Ihr Projekt</h2>
  <p>Sobald wir Ihre Anfrage geprüft haben, sehen Sie hier Ihr Projekt mit Umfang und Stand.</p>
</div>
<?php else: ?>
<div class="karte">
  <h2>Ihr Projekt</h2>
  <ul class="pruefliste">
    <li><span>Projekt</span><span><?= Html::e(Format::text((string) $projekt['title'])) ?></span></li>
    <li><span>Umfang</span><span><?= Html::e(Preise::name((string) $projekt['package'])) ?></span></li>
    <li><span>Stand</span><span><?= Html::e(Projektstatus::kundentext((string) $projekt['status'])) ?></span></li>
  </ul>

<?php if ((string) $projekt['status'] === Projektstatus::PAUSIERT): ?>
  <p class="meldung" role="status">Pausiert — <?= Html::e(Format::text($projekt['pause_reason'] === null ? null : (string) $projekt['pause_reason'])) ?></p>
<?php endif; ?>

  <ol class="stationen">
<?php foreach (PortalSteuerung::stationen((string) $projekt['status']) as $station): ?>
    <li data-stand="<?= Html::e($station['stand']) ?>"><?= Html::e($station['name']) ?></li>
<?php endforeach; ?>
  </ol>
</div>
<?php endif; ?>
