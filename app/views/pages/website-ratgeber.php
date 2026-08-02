<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Ratgeber;
use Sartu\Services\Websitetexte;

/**
 * Ein Ratgeber- oder Transparenzartikel — Website-Lastenheft §11a und §12.
 *
 * **Ein Template für beide.** §12 sagt selbst, dass sie für Leser dasselbe sind. Die
 * Reihenfolge ist überall gleich: H1 → Kurzantwort → Stand → Abschnitte → Tabelle →
 * Verweise → Aufruf.
 *
 * **Die Kurzantwort steht als erster Absatz**, nicht nach einer Hinführung. §11a: „Antwort
 * zuerst — die ersten 40–60 Wörter beantworten die Titelfrage direkt und mit Zahl."
 *
 * **Das Aktualisierungsdatum ist sichtbar** (§11a). Eine Preisangabe ohne Datum ist eine
 * Preisangabe, deren Alter niemand kennt.
 *
 * @var array<string,mixed> $artikel
 * @var array<string,string>|null $auftragslage
 * @var string $preishinweis
 */

?>
<article>
<section class="aufmacher">
  <div class="bahn schmal">
    <h1><?= Html::e((string) $artikel['h1']) ?></h1>
    <p class="lede"><?= Html::e((string) $artikel['kurzantwort']) ?></p>
    <p class="marken">Stand <?= Html::e(Format::datum(Ratgeber::STAND)) ?></p>
  </div>
</section>

<?php foreach ($artikel['abschnitte'] as $nummer => $abschnitt): ?>
<section class="abschnitt<?= $nummer % 2 === 1 ? ' abschnitt--sand' : '' ?>">
  <div class="bahn schmal">
    <h2><?= Html::e($abschnitt['h2']) ?></h2>
<?php foreach ($abschnitt['absaetze'] as $absatz): ?>
    <p><?= Html::e($absatz) ?></p>
<?php endforeach; ?>
  </div>
</section>
<?php endforeach; ?>

<?php if ($artikel['tabelle'] !== null): ?>
<section class="abschnitt">
  <div class="bahn">
    <h2>Im Vergleich</h2>
    <div class="tabellenrolle">
      <table class="zahlentabelle">
        <thead>
          <tr>
<?php foreach ($artikel['tabelle']['kopf'] as $spalte): ?>
            <th scope="col"><?= Html::e($spalte) ?></th>
<?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
<?php foreach ($artikel['tabelle']['zeilen'] as $zeile): ?>
          <tr>
<?php foreach ($zeile as $index => $wert): ?>
<?php if ($index === 0): ?>
            <th scope="row"><?= Html::e($wert) ?></th>
<?php else: ?>
            <td><?= Html::e($wert) ?></td>
<?php endif; ?>
<?php endforeach; ?>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <p class="preishinweis"><?= Html::e($preishinweis) ?></p>
  </div>
</section>
<?php endif; ?>

<section class="abschnitt abschnitt--sand">
  <div class="bahn schmal">
    <h2>Weiterlesen</h2>
    <ul class="hakenliste">
<?php foreach ($artikel['verweise'] as $verweis): ?>
      <li><a href="<?= Html::e($verweis[0]) ?>"><?= Html::e($verweis[1]) ?></a></li>
<?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="abschluss">
  <div class="bahn">
    <h2>Welche Website passt zu Ihrem Unternehmen?</h2>
    <?= Ansicht::teil('partials/handlungsblock', [
        'auftragslage' => $auftragslage,
        'preishinweis' => Websitetexte::ABSCHLUSSHINWEIS . ' ' . $preishinweis,
        'zweitziel'    => $artikel['ziel'][0],
        'zweittext'    => $artikel['ziel'][1],
        'dunkel'       => true,
    ]) ?>
  </div>
</section>
</article>
