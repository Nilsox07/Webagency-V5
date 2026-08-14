<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Html;

/**
 * Der Aufmacher jeder Unterseite — gebaut am 14.08.2026.
 *
 * ## Warum es ihn gibt
 *
 * Gemessen am 14.08.2026 bei 1440 px: Auf der Startseite ist der Aufmacher ein eigenes
 * Bauteil — zweispaltig, Bild rechts, Vertrauenszeile, drei Diagonalbänder im Hintergrund.
 * Auf **allen zwölf** Unterseitenvorlagen folgte auf die Brotkrumen unmittelbar die H1 und
 * die Handlung. `.aufmacher` trägt `min-height: clamp(560px, 79vh, 1060px)`; ohne Vorzeile,
 * ohne Vorspann und ohne Bild blieb davon eine sehr grosse Überschrift in sehr viel
 * Leerraum. Der Füllgrad der Leistungsseiten lag bei 16 bis 20 %.
 *
 * ## Was er trägt und was nicht
 *
 * Vorzeile · H1 · Vorspann von zwei bis drei Zeilen · **eine** primäre Handlung. Nicht die
 * Vertrauenszeile, nicht die Branchenzeile, nicht das Gerät — die gehören der Startseite und
 * verlören ihr Gewicht, wenn sie auf siebzehn Seiten stünden.
 *
 * **Zwei Bänder statt drei.** Der Aufmacher der Unterseite ist rund halb so hoch; drei
 * übereinanderliegende Wogen in halber Höhe lesen sich als Streifenmuster statt als Tiefe.
 * Es bleiben `band--1` (oben) und `band--3` (das durchscheinende Lime unten) — dasselbe
 * Bauteil, dieselben Farben aus `tokens.css`, keine neue Form.
 *
 * Die Bewegung hält `prefers-reduced-motion` ein; die Regel dazu steht am Ende von
 * `tokens.css` und gilt für `.woge` und `.band` gemeinsam. Ohne JavaScript sind die Bänder
 * unverändert da — sie tragen nichts, sie sind `aria-hidden`.
 *
 * @var string $vorzeile        die Zeile über der Überschrift, Monoschrift
 * @var string $h1
 * @var string $vorspann        zwei bis drei Zeilen
 * @var array<string,string>|null $auftragslage
 * @var string $preishinweis
 * @var string|null $zweitziel
 * @var string|null $zweittext
 * @var string|null $akzent     der Schluss der H1, im Lime-Marker — sonst null
 * @var array{0:string,1:string}|null $hauptziel  eigene primäre Handlung statt `/briefing`
 */

$akzent = $akzent ?? null;
$hauptziel = $hauptziel ?? null;

?>
<section class="aufmacher aufmacher--seite">
  <?php /* Zierde, sonst nichts — wie auf der Startseite: `aria-hidden`, kein Text, keine
           Handlung. */ ?>
  <div class="baender" aria-hidden="true">
    <span class="woge woge--1"><span class="band band--1"></span></span>
    <span class="woge woge--2 woge--spaet"><span class="band band--3"></span></span>
  </div>

  <?php /* **Bahn und Textspalte sind zwei Elemente, nicht eines.** `.aufmacher` ist ein
           Flex-Behaelter; ein Kind mit `margin: 0 auto` und einer engen `max-width` wird
           darin **mittig** gesetzt statt links. Beim ersten Bau trugen beide Klassen
           dasselbe `div`, und der ganze Aufmacher stand 334 px eingerueckt neben
           linksbuendigen Brotkrumen. */ ?>
  <div class="bahn">
    <div class="aufmacher__seite">
      <p class="vorzeile"><?= Html::e($vorzeile) ?></p>
      <h1><?= Html::e($h1) ?><?php if ($akzent !== null): ?> <span class="akzent"><?= Html::e($akzent) ?></span><?php endif; ?></h1>
      <p class="lede"><?= Html::e($vorspann) ?></p>

<?php if ($hauptziel !== null): ?>
      <?php /* **Ratgeber und Lexikon führen nicht zum Bedarfsscheck.** `17_SEITEN_SARTU.md`
               §7 und §8 binden je Seite ein eigenes Ziel — beim Ratgeberartikel „Firmenwebsite
               ohne WordPress" ist es `/leistung-webdesign`, beim Lexikoneintrag die passende
               Leistungsseite. Ein zweiter Knopf zum Bedarfsscheck daneben wäre die zweite
               gleichwertige Handlung, die auf diesen Seiten gerade abgeräumt wird. */ ?>
      <p class="handlung__knoepfe">
        <a class="knopf" href="<?= Html::e($hauptziel[0]) ?>"><?= Html::e($hauptziel[1]) ?><span class="pfeil" aria-hidden="true">→</span></a>
<?php if (($zweitziel ?? null) !== null): ?>
        <a class="textlink" href="<?= Html::e($zweitziel) ?>"><?= Html::e((string) $zweittext) ?><span class="pfeil" aria-hidden="true">→</span></a>
<?php endif; ?>
      </p>
<?php else: ?>
      <?= Ansicht::teil('partials/handlungsblock', [
          'auftragslage' => $auftragslage ?? null,
          'preishinweis' => $preishinweis ?? '',
          'zweitziel'    => $zweitziel ?? null,
          'zweittext'    => $zweittext ?? null,
      ]) ?>
<?php endif; ?>
    </div>
  </div>
</section>
