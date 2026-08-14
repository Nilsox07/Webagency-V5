<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Services\Websitetexte;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Ratgeber;

/**
 * `/ratgeber` — Website-Lastenheft §12, Hub.
 *
 * **Kein Kategorienfilter bei wenigen Artikeln** (§12). Fünf Einträge brauchen keine
 * Sortierung, sie brauchen eine Liste.
 *
 * Der Hub listet Ratgeber **und** Transparenzseiten, „weil sie für Leser dasselbe sind".
 *
 * @var array<string,string>|null $auftragslage
 * @var string $preishinweis
 */

?>
<?= Ansicht::teil('partials/seitenaufmacher', [
    'vorzeile'     => 'Ratgeber',
    'h1'           => Ratgeber::HUB_H1,
    'vorspann'     => Ratgeber::HUB_INTRO,
    'auftragslage' => $auftragslage ?? null,
    'preishinweis' => '',
    'zweitziel'    => '/lexikon',
    'zweittext'    => 'Zum Lexikon',
]) ?>

<section class="abschnitt abschnitt--sand">
  <div class="bahn">
    <ul class="leistungszeilen">
<?php foreach (Ratgeber::alle() as $schluessel => $artikel): ?>
      <li>
        <h2><a href="/ratgeber/<?= Html::e($schluessel) ?>"><?= Html::e($artikel['h1']) ?></a></h2>
        <p><?= Html::e($artikel['kurzantwort']) ?></p>
        <p class="marken">Stand <?= Html::e(Format::datum(Ratgeber::STAND)) ?></p>
      </li>
<?php endforeach; ?>
    </ul>
  </div>
</section>

<?php /* `17_SEITEN_SARTU.md` §6 nennt den Ratgeber-Hub als einen der vier Orte, aus denen
         `/foerderung` verlinkt wird. Sie ist keine Ratgeberseite und steht deshalb nicht in
         der Liste darueber, sondern daneben. */ ?>
<section class="abschnitt">
  <div class="bahn">
    <h2>Dazu passt eine eigene Seite.</h2>
    <p>Ob es für eine Firmenwebsite Förderung gibt, was die sechzehn Länder verlangen und
    warum der Antrag vor dem Auftrag stehen muss.</p>
    <p><a class="textlink" href="/foerderung">Zur Förderseite</a></p>
  </div>
</section>

<?php /* Die Zusage — randlos dunkel, ein Satz. Bis zum 14.08.2026 trug diese Seite weder
         eine dunkle noch eine Sandfläche: Aufmacher, dann eine weisse Liste, dann der
         Fussbereich. Der Satz stand vorher im Vorspann und ist dorthin gezogen, wo er
         Gewicht bekommt. */ ?>
<section class="zusage">
  <div class="bahn">
    <p><?= Html::e(Ratgeber::HUB_ZUSAGE) ?></p>
  </div>
</section>

<?php /* **Der Abschluss hat hier gefehlt.** Beide Übersichten endeten mit der Liste; jede
         andere Seite des Auftritts schliesst mit dem Handlungsfeld. Wer unten ankommt,
         stand vor dem Fussbereich. */ ?>
<section class="abschluss">
  <div class="bahn">
    <div class="handlungsfeld">
      <div class="handlungsfeld__text">
        <h2>Welche Website passt zu Ihrem Unternehmen?</h2>
      </div>

      <div class="handlungsfeld__handlung">
        <?= Ansicht::teil('partials/handlungsblock', [
            'auftragslage' => $auftragslage ?? null,
            'preishinweis' => Websitetexte::ABSCHLUSSHINWEIS . ' ' . ($preishinweis ?? ''),
            'zweitziel'    => '/preise',
            'zweittext'    => 'Preise ansehen',
            'dunkel'       => true,
        ]) ?>
      </div>
    </div>
  </div>
</section>
