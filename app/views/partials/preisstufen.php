<?php

declare(strict_types=1);

use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Preisstufen;
use Sartu\Services\Preise;

/**
 * Die vier Stufen — `spezifikation/10_WEBSITE_SARTU.md` Sektion 4.
 *
 * **Jede Zahl kommt aus `Preise::tabelle()`.** §11a, technische Pflicht: „Alle Preise,
 * Umfangsgrenzen, Korrekturrunden und Lieferkorridore stehen an einer Stelle im Code und
 * werden von dort auf allen Seiten ausgegeben. Eine veraltete Preisangabe ist schlimmer als
 * keine — sie wird zitiert und dann gegen SARTU verwendet."
 *
 * ## Vier Karten in einer Zeile, seit dem 13.08.2026
 *
 * Sektion 4 verlangt es wörtlich: „**Darstellung: alle vier Stufen nebeneinander.**
 * Sonderprojekt stand als Querblock **unter** den drei Paketen, obwohl §4 es als vierte Stufe
 * mit eigenem Knopf führt." Gebaut war der Querblock. Möglich wurde die vierte Spalte durch
 * den fließenden Satzspiegel — der stand bis zum selben Tag fest auf 1180 px und ist jetzt
 * der Wert aus `07_MARKE_UND_GESTALTUNG.md`.
 *
 * **Sonderprojekt ist sichtbar kein Paket** — gestrichelte Kante statt Papierfläche. Die
 * Pflichtzeile bleibt direkt unter seinem Knopf.
 *
 * ## Was von der Karte heruntergenommen wurde
 *
 * Drei Angaben standen doppelt oder gehören auf `/preise`, nicht in den Aufriss:
 *
 * | Weg | Warum |
 * |---|---|
 * | `Umfang`-Zeile | wiederholte Wort für Wort die ersten beiden Punkte der Liste darunter |
 * | `Erstes Jahr` | dritte Zahl in einer Karte, die eine Zahl vergleichbar machen soll. Sie steht vollständig in der Tabelle auf `/preise` |
 * | Lieferkorridor | Sektion 4 fordert ihn nicht; er hängt an der Freigabe, nicht am Preis |
 *
 * Sektion 4 bindet, was **bleibt**: die Zahlen, die vier Knopfbeschriftungen, das Badge an
 * Platzhirsch und die Pflichtzeile beim Sonderprojekt.
 *
 * **Gleiche Informationstiefe für alle vier.** Sektion 4: „Die Empfehlung wird durch
 * Gestaltung hervorgehoben, nicht dadurch, dass die anderen weniger erklärt bekommen."
 * Deshalb läuft dieselbe Schleife über alle vier Zeilen — und jede trägt gleich viele Punkte.
 *
 * @var string $preishinweis
 */

$stufen = Preisstufen::alle();

?>
<div class="preisstufen">
<?php foreach ($stufen as $schluessel => $stufe): ?>
<?php
$zeile  = Preise::zeile($schluessel);
$sonder = $schluessel === 'sonderprojekt';
$klasse = 'stufe';

if ($stufe['empfehlung']) {
    $klasse .= ' stufe--empfehlung';
}

if ($sonder) {
    $klasse .= ' stufe--sonder';
}
?>
  <article class="<?= $klasse ?>">
<?php if ($stufe['empfehlung']): ?>
    <p class="marke marke--empfehlung">Empfehlung</p>
<?php endif; ?>
    <p class="stufe__kicker"><?= Html::e($stufe['kicker']) ?></p>
    <h3><?= Html::e((string) $zeile['name']) ?></h3>

    <p class="preis">
      <?= $zeile['ab_preis'] ? 'ab ' : '' ?><?= Html::e(Format::euro((int) $zeile['einmalig_cent'])) ?>
      <small>einmalig <?= $zeile['ab_preis'] ? 'zzgl. mindestens' : '+' ?>
        <?= Html::e(Format::euro((int) $zeile['monatlich_cent'])) ?> im Monat</small>
    </p>

    <p class="stufe__satz"><?= Html::e($stufe['satz']) ?></p>

    <ul class="stufe__liste">
<?php foreach ($stufe['merkmale'] as $merkmal): ?>
      <li><?= Html::e($merkmal) ?></li>
<?php endforeach; ?>
    </ul>

    <p class="stufe__knopf">
      <a class="knopf<?= $stufe['empfehlung'] ? '' : ' knopf--ruhig' ?>"
         href="<?= $sonder ? '/kontakt' : '/briefing' ?>"><?= Html::e($stufe['knopf']) ?><span
         class="pfeil" aria-hidden="true">→</span></a>
    </p>
<?php if ($sonder): ?>
    <p class="stufe__pflicht"><?= Html::e(\Sartu\Services\Websitetexte::SONDERPROJEKT_TERMIN) ?></p>
<?php endif; ?>
  </article>
<?php endforeach; ?>
</div>

<p class="preisrahmen">Erstlaufzeit 12 Monate · Zahlungsziel 10 Tage</p>
<p class="preishinweis"><?= Html::e($preishinweis) ?></p>
