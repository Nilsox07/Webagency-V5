<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Html;
use Sartu\Services\Angebotstexte;
use Sartu\Services\Preise;
use Sartu\Services\Unterseitentexte as T;
use Sartu\Services\Websitetexte;

/**
 * `/ablauf` — Website-Lastenheft §8, acht Sektionen.
 *
 * **Der Lieferkorridor kommt aus `Angebotstexte::lieferkorridor()`.** Dieselbe Quelle, aus
 * der das Angebot ihn vorbelegt. §11a, technische Pflicht: „Alle Preise, Umfangsgrenzen,
 * Korrekturrunden und Lieferkorridore stehen an einer Stelle im Code."
 *
 * @var array<string,string>|null $auftragslage
 * @var string $preishinweis
 */

?>
<section class="aufmacher">
  <div class="bahn">
    <h1><?= Html::e(T::ABLAUF_H1) ?></h1>
    <p class="lede"><?= Html::e(T::ABLAUF_LEAD) ?></p>
    <p><?= Html::e(Websitetexte::TROTZDEM_ERREICHBAR) ?></p>

    <?= Ansicht::teil('partials/handlungsblock', [
        'auftragslage' => $auftragslage,
        'preishinweis' => $preishinweis,
        'zweitziel'    => '/preise',
        'zweittext'    => 'Preise ansehen',
    ]) ?>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn">
    <h2>Was sich gegenüber dem üblichen Weg ändert.</h2>

    <div class="tabellenrolle">
      <table class="zahlentabelle">
        <thead>
          <tr><th scope="col">Klassisches Projekt</th><th scope="col">Bei SARTU</th></tr>
        </thead>
        <tbody>
<?php foreach (T::VERGLEICH as $klassisch => $sartu): ?>
          <tr><td><?= Html::e($klassisch) ?></td><td><?= Html::e($sartu) ?></td></tr>
<?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn">
    <h2>Acht Schritte, davon fünf ohne Ihr Zutun.</h2>

    <ol class="zeitstrahl">
<?php foreach (T::achtSchritte() as $nummer => $schritt): ?>
      <li>
        <h3><?= (int) $nummer + 1 ?>. <?= Html::e($schritt['titel']) ?></h3>
        <p><?= Html::e($schritt['text']) ?></p>
      </li>
<?php endforeach; ?>
    </ol>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn">
    <h2>So sieht Ihr Bereich dabei aus.</h2>

    <div class="dreispalten">
<?php foreach ([
    ['sartu-portal-angebot-muster', 'Angebot mit Umfang, Preis und Zahlungsplan'],
    ['sartu-portal-briefing-muster', 'Die Fragen zu Ihrem Betrieb'],
    ['sartu-portal-domain-muster', 'Der Stand Ihrer Domain'],
    ['sartu-portal-vorschau-muster', 'Vorschau und gesammelte Rückmeldung'],
    ['sartu-portal-pflege-muster', 'Öffnungszeiten, die Sie selbst pflegen'],
] as $bild): ?>
      <?= Ansicht::teil('partials/bildplatz', [
          'name'  => $bild[0],
          'masse' => '960 × 600',
          'satz'  => $bild[1],
          'marke' => Websitetexte::MUSTERANSICHT,
      ]) ?>
<?php endforeach; ?>
    </div>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn">
    <div class="zweispalten">
      <div>
        <h2>Fünf Dinge tun Sie.</h2>
        <ul class="hakenliste">
<?php foreach (T::KUNDE_TUT as $punkt): ?>
          <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h2>Acht Dinge entscheiden wir.</h2>
        <ul class="hakenliste">
<?php foreach (T::SARTU_ENTSCHEIDET as $punkt): ?>
          <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn schmal">
    <h2>Wie lange es dauert.</h2>

    <ul class="hakenliste">
<?php foreach (['start', 'wachstum', 'platzhirsch'] as $schluessel): ?>
<?php $korridor = Angebotstexte::lieferkorridor($schluessel); ?>
      <li><?= Html::e(Preise::name($schluessel)) ?>: <?= (int) $korridor[0] ?>–<?= (int) $korridor[1] ?> Werktage</li>
<?php endforeach; ?>
    </ul>

    <p><?= Html::e(T::ZEITRAHMEN_BEDINGUNG) ?></p>
  </div>
</section>

<section class="abschluss">
  <div class="bahn">
    <h2>Der erste Schritt dauert etwa drei Minuten.</h2>
    <?= Ansicht::teil('partials/handlungsblock', [
        'auftragslage' => $auftragslage,
        'preishinweis' => Websitetexte::ABSCHLUSSHINWEIS . ' ' . $preishinweis,
        'zweitziel'    => '/preise',
        'zweittext'    => 'Preise ansehen',
        'dunkel'       => true,
    ]) ?>
  </div>
</section>
