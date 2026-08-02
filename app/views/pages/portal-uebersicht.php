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
 * Block 3 „Offene Punkte" steht seit dem 02.08.2026 hier — `tasks`, `invoices` und
 * `approvals` gibt es seit A2. Welche Zeilen entstehen, entscheidet `PortalSteuerung`; diese
 * Datei setzt sie nur. Ist keine offen, steht der Block nicht da (§0.3b: kein leerer Kasten).
 *
 * Block 4 „Letzte Aktivitaet" steht seit dem 02.08.2026 hier. Welche fuenf Ereignisse
 * zaehlen und wie ihr Klartext lautet, entscheidet `KundenAktivitaet` — diese Datei
 * bekommt Text und Datum und sonst nichts. Aus `audit_events` wird nie ein Feldwert, eine
 * Begruendung oder eine IP durchgereicht.
 *
 * @var array<string,mixed>|null $projekt
 * @var array<string,mixed>|null $angebot
 * @var list<array{text:string,zusatz:?string,ziel:string}> $offenePunkte
 * @var list<array{text:string,datum:string}> $aktivitaet
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

<?php if ($offenePunkte !== []): ?>
<div class="karte">
  <h2>Offene Punkte</h2>
  <ul class="punkteliste">
<?php foreach ($offenePunkte as $punkt): ?>
    <li>
      <a href="<?= Html::e($punkt['ziel']) ?>"><?= Html::e($punkt['text']) ?></a>
<?php if ($punkt['zusatz'] !== null): ?>
      <span class="punkteliste__hinweis"><?= Html::e($punkt['zusatz']) ?></span>
<?php endif; ?>
    </li>
<?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

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

<?php if ($aktivitaet !== []): ?>
<div class="karte">
  <h2>Letzte Aktivität</h2>
  <ul class="pruefliste">
<?php foreach ($aktivitaet as $eintrag): ?>
    <li><span><?= Html::e($eintrag['text']) ?></span><span><?= Html::e(Format::datum($eintrag['datum'])) ?></span></li>
<?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>
