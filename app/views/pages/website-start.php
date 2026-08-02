<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Html;
use Sartu\Services\Auftragslage;
use Sartu\Services\Leistungszeilen;
use Sartu\Services\Startseitentexte as T;
use Sartu\Services\Websitetexte;

/**
 * Die Startseite — Website-Lastenheft §5, Sektionen 1 bis 10.
 *
 * **Die Reihenfolge ist verbindlich**, ebenso die Bauform je Sektion. Kein Aufbaumuster kommt
 * mehr als zweimal vor (Design-Briefing §3.7) — deshalb wechselt der Grund zwischen hell,
 * `--sand` und randlos dunkel, und die Sektionen 5 und 10 sind die einzigen dunklen.
 *
 * **Sektion 6 und 8 fehlen mit Grund.** Begründung im Kopf von `Startseitentexte`.
 *
 * @var array<string,string>|null $auftragslage
 * @var string $preishinweis
 */

?>
<section class="aufmacher">
  <div class="bahn aufmacher__reihe">
    <div class="aufmacher__text">
      <p class="vorzeile"><?= Html::e(T::EYEBROW) ?></p>
      <h1><?= Html::e(T::H1) ?></h1>
      <p class="lede"><?= Html::e(T::LEAD) ?></p>

      <?= Ansicht::teil('partials/handlungsblock', [
          'auftragslage' => $auftragslage,
          'preishinweis' => $preishinweis,
          'zweitziel'    => '/preise',
          'zweittext'    => 'Preise ansehen',
      ]) ?>

      <ul class="vertrauenszeile">
<?php foreach (T::VERTRAUENSPUNKTE as $punkt): ?>
        <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
      </ul>

      <p class="branchenzeile"><?= Html::e(implode(' · ', T::BRANCHEN)) ?></p>
    </div>

    <div class="aufmacher__bild">
      <?= Ansicht::teil('partials/bildplatz', [
          'name'  => 'sartu-portal-cockpit-muster',
          'masse' => '720 × 540',
          'satz'  => 'Hier steht später eine Ansicht aus dem Kundenbereich mit Projektstand '
              . 'und nächstem Schritt.',
          'marke' => Websitetexte::MUSTERANSICHT,
      ]) ?>
    </div>
  </div>
</section>

<section class="abschnitt" id="kundenbereich">
  <div class="bahn">
    <p class="vorzeile">Kundenbereich</p>
    <h2><?= Html::e(T::S2_H2) ?></h2>
    <p class="lede"><?= Html::e(T::S2_ANTWORT) ?></p>
    <p><?= Html::e(Websitetexte::TROTZDEM_ERREICHBAR) ?></p>

    <div class="zweispalten">
<?php foreach (T::kundenbereich() as $ueberschrift => $punkte): ?>
      <div>
        <h3><?= Html::e($ueberschrift) ?></h3>
        <ul class="hakenliste">
<?php foreach ($punkte as $punkt): ?>
          <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
        </ul>
      </div>
<?php endforeach; ?>
    </div>

    <p class="hervor"><?= Html::e(T::S2_UNTERSCHIED) ?></p>

    <?= Ansicht::teil('partials/bildplatz', [
        'name'  => 'sartu-portal-briefing-muster',
        'masse' => '960 × 600',
        'satz'  => 'Hier steht später eine Ansicht der Aufgaben im Kundenbereich.',
        'marke' => Websitetexte::MUSTERANSICHT,
    ]) ?>

    <p><a class="textlink" href="/leistung-portal">Den Kundenbereich ansehen</a></p>
  </div>
</section>

<section class="abschnitt abschnitt--sand" id="ablauf">
  <div class="bahn">
    <h2><?= Html::e(T::S3_H2) ?></h2>

    <ol class="zeitstrahl">
<?php foreach (T::ablauf() as $nummer => $schritt): ?>
      <li>
        <h3><?= (int) $nummer + 1 ?>. <?= Html::e($schritt['titel']) ?></h3>
        <p><?= Html::e($schritt['satz']) ?></p>
<?php if ($schritt['bild']): ?>
        <?= Ansicht::teil('partials/bildplatz', [
            'name'  => 'sartu-ablauf-' . ($nummer + 1),
            'masse' => '640 × 400',
            'satz'  => 'Hier steht später eine Aufnahme zu diesem Schritt.',
            'marke' => null,
        ]) ?>
<?php endif; ?>
      </li>
<?php endforeach; ?>
    </ol>

    <p class="anteil"><?= Html::e(T::S3_IHR_ANTEIL) ?></p>
    <p class="anteil"><?= Html::e(T::S3_UNSER_ANTEIL) ?></p>

    <p><a class="textlink" href="/ablauf">Ablauf im Detail</a></p>
  </div>
</section>

<section class="abschnitt" id="preise">
  <div class="bahn">
    <h2><?= Html::e(T::S4_H2) ?></h2>
    <p class="vorzeile"><?= Html::e(T::S4_SUBLINE) ?></p>
    <p class="lede"><?= Html::e(T::S4_EINLEITUNG) ?></p>

    <?= Ansicht::teil('partials/preisstufen', ['preishinweis' => $preishinweis]) ?>

    <div class="karte karte--betont">
      <h3>Was die Monatspauschale abdeckt</h3>
      <ul class="hakenliste">
<?php foreach (T::MONATSPAUSCHALE as $punkt): ?>
        <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
      </ul>
      <p class="leise"><?= Html::e(T::SEO_GRUNDLAGE) ?></p>
    </div>
  </div>
</section>

<section class="zusage">
  <div class="bahn">
    <p><?= Html::e(T::S5_ZUSAGE) ?></p>
  </div>
</section>

<section class="abschnitt" id="leistungen">
  <div class="bahn">
    <h2><?= Html::e(T::S7_H2) ?></h2>
    <p class="lede"><?= Html::e(T::S7_EINLEITUNG) ?></p>

    <ul class="leistungszeilen">
<?php foreach (Leistungszeilen::alle() as $zeile): ?>
      <li>
        <h3><?= Html::e($zeile['titel']) ?></h3>
        <p><?= Html::e($zeile['satz']) ?></p>
        <p class="marken"><?= Html::e(implode(' · ', $zeile['tags'])) ?></p>
      </li>
<?php endforeach; ?>
    </ul>

    <p><a class="textlink" href="/leistungen">Alle Leistungen im Überblick</a></p>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn">
    <h2><?= Html::e(T::S7_SEO_H2) ?></h2>
    <p class="lede"><?= Html::e(T::S7_SEO_TEXT) ?></p>

    <div class="dreispalten">
<?php foreach (T::seoSpalten() as $spalte): ?>
      <div>
        <h3><?= Html::e($spalte['titel']) ?></h3>
        <p><?= Html::e($spalte['satz']) ?></p>
      </div>
<?php endforeach; ?>
    </div>

    <p class="leise"><?= Html::e(Websitetexte::KEINE_RANKINGZUSAGE) ?></p>
  </div>
</section>

<section class="abschnitt" id="fragen">
  <div class="bahn schmal">
    <h2>Häufige Fragen</h2>
    <?= Ansicht::teil('partials/fragenliste', ['fragen' => T::fragen()]) ?>
  </div>
</section>

<section class="abschluss" id="bedarfsscheck">
  <div class="bahn">
    <h2><?= Html::e(T::S10_H2) ?></h2>
    <p class="lede"><?= Html::e(T::S10_TEXT) ?></p>

    <p class="marken"><?= Html::e(implode(' · ', T::S10_CHIPS)) ?></p>

    <ul class="vertrauenszeile">
<?php foreach (T::S10_VERTRAUEN as $punkt): ?>
      <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
    </ul>

    <?= Ansicht::teil('partials/handlungsblock', [
        'auftragslage' => $auftragslage,
        'preishinweis' => Websitetexte::ABSCHLUSSHINWEIS . ' ' . $preishinweis,
        'zweitziel'    => '/preise',
        'zweittext'    => 'Preise ansehen',
        'dunkel'       => true,
    ]) ?>
  </div>
</section>
