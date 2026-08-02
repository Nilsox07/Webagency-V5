<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Html;
use Sartu\Services\Leistungszeilen;
use Sartu\Services\Preise;
use Sartu\Services\Unterseitentexte as T;
use Sartu\Services\Websitetexte;

/**
 * `/leistungen` — Website-Lastenheft §6, sieben Sektionen.
 *
 * **Dieselben acht Zeilen wie die Startseite, nur tiefer.** §6 Sektion 3. Sie kommen aus
 * `Leistungszeilen::alle()`; die Startseite nimmt `satz`, diese Seite nimmt `ausfuehrlich`.
 *
 * @var array<string,string>|null $auftragslage
 * @var string $preishinweis
 */

?>
<section class="aufmacher">
  <div class="bahn">
    <h1><?= Html::e(T::LEISTUNGEN_H1) ?></h1>
    <p class="lede"><?= Html::e(T::LEISTUNGEN_LEAD) ?></p>

    <?= Ansicht::teil('partials/handlungsblock', [
        'auftragslage' => $auftragslage,
        'preishinweis' => $preishinweis,
        'zweitziel'    => '/preise',
        'zweittext'    => 'Preise ansehen',
    ]) ?>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn schmal">
    <h2>Kurz gesagt</h2>
    <p class="lede"><?= Html::e(T::LEISTUNGEN_KURZ) ?></p>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn">
    <h2>Was in jedem Angebot steckt.</h2>

    <ul class="leistungszeilen">
<?php foreach (Leistungszeilen::alle() as $zeile): ?>
      <li>
        <h3><?= Html::e($zeile['titel']) ?></h3>
<?php foreach ($zeile['ausfuehrlich'] as $satz): ?>
        <p><?= Html::e($satz) ?></p>
<?php endforeach; ?>
        <p class="marken"><?= Html::e(implode(' · ', $zeile['tags'])) ?></p>
<?php if ($zeile['ziel'] !== null): ?>
        <p><a class="textlink" href="<?= Html::e($zeile['ziel']) ?>"><?= Html::e($zeile['titel']) ?> im Einzelnen</a></p>
<?php endif; ?>
      </li>
<?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn schmal">
    <h2>Sieben Entscheidungen nehmen wir Ihnen ab.</h2>
    <ul class="hakenliste">
<?php foreach (Leistungszeilen::NICHT_ENTSCHEIDEN as $punkt): ?>
      <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn">
    <h2>Wie tief es je Umfang geht.</h2>

    <div class="tabellenrolle">
      <table class="zahlentabelle">
        <caption class="leise">Was Sie am Ende bekommen — kein Häkchenvergleich.</caption>
        <thead>
          <tr><th scope="col">Umfang</th><th scope="col">Ergebnis</th></tr>
        </thead>
        <tbody>
<?php foreach (T::tiefeJeStufe() as $schluessel => $ergebnis): ?>
          <tr>
            <th scope="row"><?= Html::e(Preise::name($schluessel)) ?></th>
            <td><?= Html::e($ergebnis) ?></td>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <p><a class="textlink" href="/preise">Preise ansehen</a></p>
  </div>
</section>

<section class="abschnitt abschnitt--sand" id="fragen">
  <div class="bahn schmal">
    <h2>Häufige Fragen</h2>
    <?= Ansicht::teil('partials/fragenliste', ['fragen' => T::leistungenFragen()]) ?>
  </div>
</section>

<section class="abschluss">
  <div class="bahn">
    <h2>Welche Website passt zu Ihrem Unternehmen?</h2>
    <?= Ansicht::teil('partials/handlungsblock', [
        'auftragslage' => $auftragslage,
        'preishinweis' => Websitetexte::ABSCHLUSSHINWEIS . ' ' . $preishinweis,
        'zweitziel'    => '/ablauf',
        'zweittext'    => 'Ablauf ansehen',
        'dunkel'       => true,
    ]) ?>
  </div>
</section>
