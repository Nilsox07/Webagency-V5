<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Services\Websitetexte;
use Sartu\Helpers\Html;
use Sartu\Services\Lexikon;

/**
 * `/lexikon` — Website-Lastenheft §13, Hub.
 *
 * **Kein Suchfeld bei acht Begriffen** (§13, erst ab etwa 40). Eine Suche über acht Zeilen
 * kostet einen Klick und spart keinen.
 *
 * Alphabetisch sortiert, wie §13 es verlangt — nach dem Begriff, nicht nach dem Schlüssel.
 *
 * @var list<array{schluessel:string,begriff:string,kurz:string}> $begriffe
 */

?>
<?= Ansicht::teil('partials/seitenaufmacher', [
    'vorzeile'     => 'Lexikon',
    'h1'           => Lexikon::HUB_H1,
    'vorspann'     => Lexikon::HUB_INTRO,
    'auftragslage' => $auftragslage ?? null,
    'preishinweis' => '',
    'zweitziel'    => '/ratgeber',
    'zweittext'    => 'Zum Ratgeber',
]) ?>

<section class="abschnitt abschnitt--sand">
  <div class="bahn">
    <ul class="leistungszeilen">
<?php foreach ($begriffe as $eintrag): ?>
      <li>
        <h2><a href="/lexikon/<?= Html::e($eintrag['schluessel']) ?>"><?= Html::e($eintrag['begriff']) ?></a></h2>
        <p><?= Html::e($eintrag['kurz']) ?></p>
      </li>
<?php endforeach; ?>
    </ul>
  </div>
</section>

<?php /* Die Zusage — randlos dunkel, ein Satz. Bis zum 14.08.2026 trug diese Seite weder
         eine dunkle noch eine Sandfläche: Aufmacher, dann eine weisse Liste, dann der
         Fussbereich. Der Satz stand vorher im Vorspann und ist dorthin gezogen, wo er
         Gewicht bekommt. */ ?>
<section class="zusage">
  <div class="bahn">
    <p><?= Html::e(Lexikon::HUB_ZUSAGE) ?></p>
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
            'zweitziel'    => '/leistungen',
            'zweittext'    => 'Leistungen ansehen',
            'dunkel'       => true,
        ]) ?>
      </div>
    </div>
  </div>
</section>
