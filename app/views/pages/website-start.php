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
 * `--sand` und dunkel.
 *
 * **Der Grund je Sektion ist am 13.08.2026 gegen `design/startseite.html` nachgemessen
 * worden.** Hier stand vorher, die Sektionen 5 und 10 seien die einzigen dunklen; der
 * Entwurf hat drei dunkle Flächen, und die grösste fehlte ganz: Sektion 2 und 3 bilden dort
 * **einen** durchgehenden Block, oben und unten mit `--r-xl` gerundet. Sektion 10 ist im
 * Entwurf **hell** und trägt die dunkle Fläche als `.handlungsfeld` in sich.
 * `MarkupTest::testDieGrundfolgeDerStartseiteStimmtMitDemEntwurf` hält die Folge fest.
 *
 * **Sektion 8 fehlt mit Grund.** Begründung im Kopf von `Startseitentexte`.
 *
 * **Sektion 6 steht seit dem 13.08.2026 da — wenn die Daten sie tragen.** §4c hat die Sperre
 * von „nicht gebaut" auf „nicht gefüllt" umgestellt; `partials/gruender` entscheidet das.
 *
 * @var array<string,string>|null $auftragslage
 * @var array{name:string,text:string,bild:string}|null $gruender
 * @var string $preishinweis
 */

?>
<section class="aufmacher">
  <?php /* Zierde, sonst nichts. `aria-hidden`, kein Text, keine Handlung — und die
           Bewegung haelt `prefers-reduced-motion` ein (tokens.css, letzter Block).
           Ohne JavaScript sind die Baender unveraendert da; sie tragen nichts. */ ?>
  <div class="baender" aria-hidden="true">
    <span class="woge woge--1"><span class="band band--1"></span></span>
    <span class="woge woge--2"><span class="band band--2"></span></span>
    <span class="woge woge--1 woge--spaet"><span class="band band--3"></span></span>
  </div>

  <div class="bahn aufmacher__reihe">
    <div class="aufmacher__text">
      <p class="vorzeile"><?= Html::e(T::EYEBROW) ?></p>
      <?php /* Zwei gebundene Teile statt einer Konstante — nur so kann der Lime-Marker
               am Schluss stehen (`design/startseite.html`, `.accent`). Der Wortlaut ist
               unveraendert; `Startseitentexte::h1Vollstaendig()` haelt das fest. */ ?>
      <h1><?= Html::e(T::H1_ANFANG) ?> <span class="akzent"><?= Html::e(T::H1_SCHLUSS) ?></span></h1>
      <p class="lede"><?= Html::e(T::LEAD) ?></p>

      <?= Ansicht::teil('partials/handlungsblock', [
          'auftragslage' => $auftragslage,
          // Leer: Der Hinweis steht unten in der Leiste (§5 Sektion 1, Gruppe 3).
          'preishinweis' => '',
          'zweitziel'    => '/preise',
          'zweittext'    => 'Preise ansehen',
      ]) ?>

      <?php /* Gruppe 3 aus §5 Sektion 1: „eine ruhige Leiste am Fuss". Die Trennlinie
               darueber markiert den Gruppenwechsel — im Entwurf `border-top` an
               `.hero-trust`. Sie fehlte bis zum 10.08.2026, und ohne sie standen die
               Pflichtangaben als vierter gleichrangiger Block da. */ ?>
      <div class="aufmacher__leiste">
        <ul class="vertrauenszeile">
<?php foreach (T::VERTRAUENSPUNKTE as $punkt): ?>
          <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
        </ul>

        <p class="preishinweis"><?= Html::e($preishinweis) ?></p>
        <p class="branchenzeile"><?= Html::e(implode(' · ', T::BRANCHEN)) ?></p>
      </div>
    </div>

    <div class="aufmacher__bild">
      <?= Ansicht::teil('partials/aufmacherbild', ['marke' => Websitetexte::MUSTERANSICHT]) ?>
    </div>
  </div>
</section>

<?php /* Sektion 2 und 3 sind im Entwurf **ein** dunkler Block, oben und unten mit
         --r-xl gerundet (`design/startseite.html` Zeile 859 und 873). */ ?>
<section class="abschnitt abschnitt--dunkel abschnitt--rundoben" id="kundenbereich">
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

<section class="abschnitt abschnitt--dunkel abschnitt--rundunten" id="ablauf">
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

<?php /* Sektion 6 — steht hier, wenn Name, Text und Bild in den Betreiberdaten stehen, und
         entfaellt sonst vollstaendig (§4c). Die Pruefung liegt im Partial, nicht hier. */ ?>
<?= Ansicht::teil('partials/gruender', ['gruender' => $gruender]) ?>

<section class="abschnitt abschnitt--sand" id="leistungen">
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

<section class="abschnitt">
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
    <div class="handlungsfeld">
      <div class="handlungsfeld__text">
        <h2><?= Html::e(T::S10_H2) ?></h2>
        <p class="lede"><?= Html::e(T::S10_TEXT) ?></p>

        <p class="marken"><?= Html::e(implode(' · ', T::S10_CHIPS)) ?></p>

        <ul class="vertrauenszeile">
    <?php foreach (T::S10_VERTRAUEN as $punkt): ?>
          <li><?= Html::e($punkt) ?></li>
    <?php endforeach; ?>
        </ul>
      </div>

      <div class="handlungsfeld__handlung">
        <?= Ansicht::teil('partials/handlungsblock', [
            'auftragslage' => $auftragslage,
            'preishinweis' => Websitetexte::ABSCHLUSSHINWEIS . ' ' . $preishinweis,
            'zweitziel'    => '/preise',
            'zweittext'    => 'Preise ansehen',
            'dunkel'       => true,
        ]) ?>
      </div>
    </div>
  </div>
</section>
