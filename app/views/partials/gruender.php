<?php

declare(strict_types=1);

use Sartu\Helpers\Html;
use Sartu\Services\Firmenseitentexte;
use Sartu\Services\Gruenderangaben;

/**
 * Sektion 6 „Wer dahintersteckt" — `10_WEBSITE_SARTU.md`, `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4c.
 *
 * **Der Aufrufer prüft nicht, ob die Sektion gezeigt wird — das tut diese Datei.** Sie steht
 * auf zwei Seiten; eine Prüfung je Aufrufer wäre zwei Prüfungen, und die zweite läuft
 * irgendwann auseinander.
 *
 * ## Der Platzhalter — geändert am 13.08.2026 (§4d)
 *
 * Bis dahin entfiel die Sektion vollständig, sobald eine der drei Angaben fehlte: „Ein leerer
 * Rahmen an der Vertrauensstelle ist schlechter als keine Sektion" (§5, Design-Briefing §4a).
 *
 * **Der Satz gilt für die veröffentlichte Seite und nur für sie.** Für den Bau ist der
 * unsichtbare Zustand der teurere: Wer die Sektion nie sieht, weiss nicht, dass sie fehlt,
 * und prüft ihre Gestaltung erst am Tag der Freigabe. `10_WEBSITE_SARTU.md` §5 sieht genau
 * dafür Bedingung 4a vor — die Startsperre bricht an `[[FOTO-FEHLT]]` ab.
 *
 * Der Platzhalter ist damit eine **Zwischenstufe im Bau** und kein Zustand, der live gehen
 * kann. Vorausgesetzt, jemand sucht die Markierung: `Platzhalterpruefung` tut das seit
 * demselben Tag, vorher tat es niemand.
 *
 * ## Die vier Abgrenzungen gehören dazu
 *
 * Sektion 6 bindet sie ausdrücklich neben Foto und Textlink. Sie sind der zweite Teil des
 * Belegersatzes: „Wer seine Grenzen benennt, wirkt geprüft; wer nur Vorzüge aufzählt, wirkt
 * beliebig." Sie stehen in `Firmenseitentexte::NICHT` — dieselbe Liste wie auf `/ueber-uns`,
 * nicht eine zweite Kopie.
 *
 * @var array{name:string,text:string,bild:string}|null $gruender
 * @var bool $mitAbgrenzung  auf `/ueber-uns` stehen die vier Punkte schon als eigener
 *                           Abschnitt — dort wären sie zweimal auf einer Seite
 * @var bool $mitLink        `Mehr über SARTU` zeigt auf `/ueber-uns`; auf dieser Seite
 *                           selbst wäre es ein Link auf die Seite, die man gerade liest
 */

$gruender = $gruender ?? null;
$mitAbgrenzung = $mitAbgrenzung ?? true;
$mitLink = $mitLink ?? true;

$fehlend = $gruender === null ? (new Gruenderangaben())->fehlendeAngaben() : [];

?>
<section class="abschnitt gruender" id="dahinter">
  <div class="bahn gruender__reihe">
<?php if ($gruender !== null): ?>
    <figure class="gruender__bild">
      <img src="<?= Html::e($gruender['bild']) ?>"
           alt="<?= Html::e(Gruenderangaben::bildbeschreibung($gruender['name'])) ?>"
           width="640" height="800" loading="lazy" decoding="async">
    </figure>
<?php else: ?>
    <?php /* Dieselbe Bauform wie jeder andere Bildplatz — die Markierung im Markup, nicht
             auf der Seite. Bis zum 13.08.2026 las der Besucher hier `[[FOTO-FEHLT]]
             gruender.webp · 640 × 800`; Dateiname und Pixelmasse sind Bauwissen. */ ?>
    <figure class="gruender__bild bildplatz"
            data-verhaeltnis="<?= Html::e(\Sartu\Helpers\Bildmasse::verhaeltnis(640, 800)) ?>"
            data-fehlt="<?= Html::e(Gruenderangaben::MARKIERUNG) ?> gruender.webp · 640 × 800">
      <p class="bildplatz__kennung">Platz für das Foto</p>
      <figcaption><?= Html::e(Gruenderangaben::PLATZHALTERSATZ) ?></figcaption>
    </figure>
<?php endif; ?>

    <div class="gruender__text">
      <h2><?= Html::e(Gruenderangaben::H2) ?></h2>

<?php if ($gruender !== null): ?>
      <p class="gruender__name"><?= Html::e($gruender['name']) ?></p>

<?php foreach (Gruenderangaben::absaetze($gruender['text']) as $absatz): ?>
      <p><?= Html::e($absatz) ?></p>
<?php endforeach; ?>
<?php else: ?>
      <p class="leise">Es fehlt noch: <?= Html::e(implode(' · ', $fehlend)) ?>.</p>
<?php endif; ?>

<?php if ($mitAbgrenzung): ?>
      <?php /* Vier Stichworte ergeben eine Zeile, keine vier Aufzaehlungspunkte
               (§4d, Auftrag b vom 13.08.2026). Der Wortlaut ist unveraendert — es
               ist dieselbe Liste aus `Firmenseitentexte::NICHT`. */ ?>
      <p class="abgrenzung">Was SARTU bewusst nicht ist:
        <span><?= Html::e(implode(' · ', Firmenseitentexte::NICHT)) ?></span></p>
<?php endif; ?>

<?php if ($mitLink): ?>
      <p><a class="textlink" href="<?= Html::e(Gruenderangaben::LINK_ZIEL) ?>"><?= Html::e(Gruenderangaben::LINK_TEXT) ?><span class="pfeil" aria-hidden="true">→</span></a></p>
<?php endif; ?>
    </div>
  </div>
</section>
