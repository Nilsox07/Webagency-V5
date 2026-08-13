<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Html;
use Sartu\Services\Musterprojekte;
use Sartu\Services\Musterprojektetexte as T;
use Sartu\Services\Preise;
use Sartu\Services\Preisstufen;

/**
 * `/musterprojekte` — `17_SEITEN_SARTU.md` §4a, sieben Blöcke in gebundener Reihenfolge.
 *
 * | # | Block |
 * |---|---|
 * | 1 | H1 — benennt, dass es Muster sind |
 * | 2 | Einleitung mit Gründungsjahr und Anzahl |
 * | 3 | **Warum es Muster sind und keine Kunden** |
 * | 4–6 | je Fall ein Abschnitt, Reihenfolge wie auf der Startseite |
 * | 7 | Übergang zum Bedarfsscheck |
 *
 * **Je Fall dieselben fünf Bestandteile wie auf der Karte, nur ausgeschrieben** — dazu der
 * eine Block, den die Karte nicht trägt: *warum diese Stufe und nicht die nächstkleinere.*
 *
 * ## Der Preishinweis steht in der langen Fassung
 *
 * §4a bindet ihn, „sobald eine Zahl fällt". Hier fällt sie: Jeder Fall nennt seinen Umfang,
 * und der Umfang trägt einen Preis. Er kommt aus `Preise::preiszeile()`, nicht aus einer
 * zweiten Schreibweise derselben Zahl.
 *
 * @var string $preishinweis
 * @var array<string,string>|null $auftragslage
 */

?>
<section class="aufmacher">
  <div class="bahn schmal">
    <h1><?= Html::e(T::H1) ?></h1>
    <p class="lede"><?= Html::e(T::einleitung()) ?></p>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn schmal">
    <h2><?= Html::e(T::WARUM_H2) ?></h2>
<?php foreach (T::WARUM as $absatz): ?>
    <p><?= Html::e($absatz) ?></p>
<?php endforeach; ?>
  </div>
</section>

<?php foreach (Musterprojekte::alle() as $schluessel => $projekt): ?>
<?php $stufe = Preisstufen::alle()[$projekt['paket']] ?? null; ?>
<section class="abschnitt musterfall" id="muster-<?= Html::e($schluessel) ?>">
  <div class="bahn schmal">
    <p class="marke"><?= Html::e(Musterprojekte::FAHNE) ?></p>
    <h2><?= Html::e($projekt['gattung']) ?></h2>

<?php foreach ($projekt['ausfuehrlich'] as $absatz): ?>
    <p><?= Html::e($absatz) ?></p>
<?php endforeach; ?>

    <?= Ansicht::teil('partials/bildplatz', [
        'name'  => Musterprojekte::bildname($schluessel),
        'masse' => '1280 × 800',
        'satz'  => Musterprojekte::bildsatz($schluessel),
    ]) ?>

    <dl class="musterteile musterteile--breit">
<?php foreach (Musterprojekte::zeilen() as $feld => $beschriftung): ?>
      <div>
        <dt><?= Html::e($beschriftung) ?></dt>
        <dd><?= Html::e((string) $projekt[$feld]) ?></dd>
      </div>
<?php endforeach; ?>
      <div>
        <dt><?= Html::e(T::WARUM_NICHT_KLEINER) ?></dt>
        <dd><?= Html::e((string) $projekt['warum_nicht_kleiner']) ?></dd>
      </div>
      <div>
        <dt><?= Html::e(T::GESTALTUNG) ?></dt>
        <dd><?= Html::e((string) $projekt['gestaltung']) ?></dd>
      </div>
    </dl>

    <p class="leise"><?= Html::e(Preise::name($projekt['paket'])) ?>:
      <?= Html::e((string) Preise::preiszeile($projekt['paket'])) ?></p>
  </div>
</section>
<?php endforeach; ?>

<section class="abschluss">
  <div class="bahn">
    <div class="handlungsfeld">
      <div class="handlungsfeld__text">
        <h2><?= Html::e(T::ABSCHLUSS_H2) ?></h2>
        <p class="lede"><?= Html::e(T::ABSCHLUSS) ?></p>
      </div>

      <div class="handlungsfeld__handlung">
        <?= Ansicht::teil('partials/handlungsblock', [
            'auftragslage' => $auftragslage,
            'preishinweis' => $preishinweis,
            'zweitziel'    => '/preise',
            'zweittext'    => 'Preise ansehen',
            'dunkel'       => true,
        ]) ?>
      </div>
    </div>
  </div>
</section>
