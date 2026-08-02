<?php

declare(strict_types=1);

use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Angebotstexte;
use Sartu\Services\Preisstufen;
use Sartu\Services\Preise;

/**
 * Die drei Stufen und das Sonderprojekt — Website-Lastenheft §5 Sektion 4, §7 Sektion 2.
 *
 * **Jede Zahl kommt aus `Preise::tabelle()`.** §11a, technische Pflicht: „Alle Preise,
 * Umfangsgrenzen, Korrekturrunden und Lieferkorridore stehen an einer Stelle im Code und
 * werden von dort auf allen Seiten ausgegeben. Eine veraltete Preisangabe ist schlimmer als
 * keine — sie wird zitiert und dann gegen SARTU verwendet."
 *
 * **Gleiche Informationstiefe für alle vier.** §5 Sektion 4: „Die Empfehlung wird durch
 * Gestaltung hervorgehoben, nicht dadurch, dass die anderen weniger erklärt bekommen. Wer
 * eine kleine Lösung braucht und nur die teure erklärt sieht, geht — und zwar zu Recht."
 * Deshalb läuft dieselbe Schleife über alle vier Zeilen.
 *
 * **Das Sonderprojekt steht als Zeile unter den drei Karten**, nicht als vierte Karte — und
 * es ist abgesetzt, nicht abgeschwächt: gleiche Schriftgröße für den Betrag, voller Kontrast,
 * durchgezogener Rahmen (UX-Audit 28.07.2026, Vorgabe 1).
 *
 * @var string $preishinweis
 */

$stufen = Preisstufen::alle();
$sonder = array_pop($stufen);

?>
<div class="preisstufen">
<?php foreach ($stufen as $schluessel => $stufe): ?>
<?php $zeile = Preise::zeile($schluessel); ?>
  <article class="stufe<?= $stufe['empfehlung'] ? ' stufe--empfehlung' : '' ?>">
<?php if ($stufe['empfehlung']): ?>
    <p class="marke">Empfehlung</p>
<?php endif; ?>
    <h3><?= Html::e((string) $zeile['name']) ?></h3>

    <p class="preis"><?= Html::e(Format::euro((int) $zeile['einmalig_cent'])) ?> einmalig</p>
    <p class="preis__zusatz">+ <?= Html::e(Format::euro((int) $zeile['monatlich_cent'])) ?> im Monat</p>
    <p class="preis__jahr">Erstes Jahr: <?= Html::e((string) Preise::erstesJahr($schluessel)) ?></p>

    <p class="umfang"><?= Html::e((string) $zeile['seiten']) ?>.
      <?= (int) $zeile['korrekturrunden'] ?> <?= (int) $zeile['korrekturrunden'] === 1 ? 'Korrekturrunde' : 'Korrekturrunden' ?>.</p>

    <p><?= Html::e($stufe['fuer_wen']) ?></p>

    <ul class="hakenliste">
<?php foreach ($stufe['merkmale'] as $merkmal): ?>
      <li><?= Html::e($merkmal) ?></li>
<?php endforeach; ?>
    </ul>

<?php $korridor = Angebotstexte::lieferkorridor($schluessel); ?>
<?php if ($korridor !== null): ?>
    <p class="leise"><?= (int) $korridor[0] ?>–<?= (int) $korridor[1] ?> Werktage nach vollständigem Start.</p>
<?php endif; ?>

    <p><a class="knopf<?= $stufe['empfehlung'] ? '' : ' knopf--ruhig' ?>" href="/briefing"><?= Html::e($stufe['knopf']) ?></a></p>
  </article>
<?php endforeach; ?>
</div>

<article class="sonderprojekt">
<?php $zeile = Preise::zeile('sonderprojekt'); ?>
  <h3><?= Html::e((string) $zeile['name']) ?></h3>

  <p class="preis">ab <?= Html::e(Format::euro((int) $zeile['einmalig_cent'])) ?> einmalig</p>
  <p class="preis__zusatz">mindestens <?= Html::e(Format::euro((int) $zeile['monatlich_cent'])) ?> im Monat</p>
  <p class="preis__jahr">Erstes Jahr: <?= Html::e((string) Preise::erstesJahr('sonderprojekt')) ?></p>

  <p class="umfang">Umfang nach technischer Vorprüfung. Kein Paket.</p>
  <p><?= Html::e($sonder['fuer_wen']) ?></p>

  <ul class="hakenliste">
<?php foreach ($sonder['merkmale'] as $merkmal): ?>
    <li><?= Html::e($merkmal) ?></li>
<?php endforeach; ?>
  </ul>

  <p><a class="knopf knopf--ruhig" href="/kontakt"><?= Html::e($sonder['knopf']) ?></a></p>
  <p class="leise"><?= Html::e(\Sartu\Services\Websitetexte::SONDERPROJEKT_TERMIN) ?></p>
</article>

<p class="preisrahmen">Erstlaufzeit 12 Monate · Zahlungsziel 10 Tage</p>
<p class="preishinweis"><?= Html::e($preishinweis) ?></p>
