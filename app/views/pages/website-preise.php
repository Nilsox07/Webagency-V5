<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Leistungszeilen;
use Sartu\Services\Preise;
use Sartu\Services\Unterseitentexte as T;
use Sartu\Services\Websitetexte;

/**
 * `/preise` — Website-Lastenheft §7, neun Sektionen.
 *
 * **Bildregel:** keine großen Fotos. §7: „Preise brauchen Scanbarkeit." Deshalb steht auf
 * dieser Seite kein Bildplatz — auch kein gekennzeichneter.
 *
 * **Die Erstjahrestabelle ist mobil waagerecht rollbar** und hat rechtsbündige Ziffern mit
 * `tabular-nums`. Ohne beides ist eine Preistabelle auf dem Telefon unlesbar, und §17
 * verbietet waagerechtes Rollen des Seitenkörpers — deshalb rollt der Kasten, nicht die Seite.
 *
 * @var array<string,string>|null $auftragslage
 * @var string $preishinweis
 */

?>
<section class="aufmacher">
  <div class="bahn">
    <h1><?= Html::e(T::PREISE_H1) ?></h1>
    <p class="lede"><?= Html::e(T::PREISE_LEAD) ?></p>

    <?= Ansicht::teil('partials/handlungsblock', [
        'auftragslage' => $auftragslage,
        'preishinweis' => $preishinweis,
        'zweitziel'    => '/ablauf',
        'zweittext'    => 'Ablauf ansehen',
    ]) ?>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn">
    <h2>Sie wählen kein Paket. Wir empfehlen einen Umfang.</h2>
    <?= Ansicht::teil('partials/preisstufen', ['preishinweis' => $preishinweis]) ?>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn">
    <h2>Was das erste Jahr kostet.</h2>

    <div class="tabellenrolle">
      <table class="zahlentabelle">
        <caption class="leise">Einmalpreis plus zwölf Monatsbeträge. Alle Werte netto.</caption>
        <thead>
          <tr>
            <th scope="col">Umfang</th>
            <th scope="col" class="zahl">Einmalig netto</th>
            <th scope="col" class="zahl">Betrieb je Monat</th>
            <th scope="col" class="zahl">Erstes Jahr netto</th>
          </tr>
        </thead>
        <tbody>
<?php foreach (Preise::tabelle() as $schluessel => $zeile): ?>
          <tr<?= $schluessel === 'platzhirsch' ? ' class="betont"' : '' ?>>
            <th scope="row"><?= Html::e((string) $zeile['name']) ?></th>
            <td class="zahl"><?= $zeile['ab_preis'] ? 'ab ' : '' ?><?= Html::e(Format::euro((int) $zeile['einmalig_cent'])) ?></td>
            <td class="zahl"><?= $zeile['ab_preis'] ? 'ab ' : '' ?><?= Html::e(Format::euro((int) $zeile['monatlich_cent'])) ?></td>
            <td class="zahl"><?= Html::e((string) Preise::erstesJahr($schluessel)) ?></td>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <p class="preishinweis"><?= Html::e($preishinweis) ?></p>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn schmal">
    <h2>Acht Bausteine stecken in jedem Projekt.</h2>
    <ul class="hakenliste">
<?php foreach (Leistungszeilen::IN_JEDEM_PROJEKT as $punkt): ?>
      <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn">
    <h2><?= Html::e(T::SCHUTZ_H2) ?></h2>

    <div class="dreispalten">
      <div>
        <h3>Enthalten</h3>
        <ul class="hakenliste">
<?php foreach (T::SCHUTZ_ENTHALTEN as $punkt): ?>
          <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h3>Nicht enthalten</h3>
        <ul class="hakenliste">
<?php foreach (T::SCHUTZ_NICHT_ENTHALTEN as $punkt): ?>
          <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h3>Pflegen Sie selbst</h3>
        <ul class="hakenliste">
<?php foreach (T::SCHUTZ_SELBST as $punkt): ?>
          <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
        </ul>
      </div>
    </div>

    <p><a class="textlink" href="/leistung-wartung">Den Rundum-Schutz im Einzelnen</a></p>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn schmal">
    <h2><?= Html::e(T::DOMAIN_H2) ?></h2>
    <p><?= Html::e(T::DOMAIN_TEXT) ?></p>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn schmal">
    <h2><?= Html::e(T::ZAHLUNG_H2) ?></h2>
    <ul class="hakenliste">
<?php foreach (T::ZAHLUNG_PLAENE as $stufe => $plan): ?>
      <li><?= Html::e($stufe) ?>: <?= Html::e($plan) ?></li>
<?php endforeach; ?>
    </ul>
    <p><?= Html::e(T::ZAHLUNG_SLOT) ?></p>
  </div>
</section>

<section class="abschnitt" id="fragen">
  <div class="bahn schmal">
    <h2>Häufige Fragen</h2>
    <?= Ansicht::teil('partials/fragenliste', ['fragen' => T::preiseFragen()]) ?>
  </div>
</section>

<section class="abschluss">
  <div class="bahn">
    <h2>Welcher Umfang passt zu Ihrem Betrieb?</h2>
    <?= Ansicht::teil('partials/handlungsblock', [
        'auftragslage' => $auftragslage,
        'preishinweis' => Websitetexte::ABSCHLUSSHINWEIS . ' ' . $preishinweis,
        'zweitziel'    => '/leistungen',
        'zweittext'    => 'Leistungen ansehen',
        'dunkel'       => true,
    ]) ?>
  </div>
</section>
