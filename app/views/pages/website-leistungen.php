<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Html;
use Sartu\Services\Branchenseiten;
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
<?= Ansicht::teil('partials/seitenaufmacher', [
    'vorzeile'     => 'Leistungen',
    'h1'           => T::LEISTUNGEN_H1,
    'vorspann'     => T::LEISTUNGEN_LEAD,
    'auftragslage' => $auftragslage,
    'preishinweis' => $preishinweis,
    'zweitziel'    => '/preise',
    'zweittext'    => 'Preise ansehen',
]) ?>

<section class="abschnitt abschnitt--sand">
  <div class="bahn schmal">
    <h2>Kurz gesagt</h2>
    <p class="lede"><?= Html::e(T::LEISTUNGEN_KURZ) ?></p>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn">
    <h2>Was in jedem Angebot steckt.</h2>

    <?php /* **Der Verteiler verweist, die Zielseite trägt.** Umgestellt am 14.08.2026.
             Gemessen davor: `/leistungen` trug 794 Wörter, die fünf Leistungsseiten
             dahinter je 263 bis 383. Der Verteiler war ausführlicher als seine Ziele — wer
             hier fertig gelesen hatte, fand dort nichts Neues mehr.

             Deshalb: Eine Zeile **mit** eigener Seite steht hier mit ihrem einen Satz und
             dem Weg dorthin. Eine Zeile **ohne** eigene Seite behält ihre Ausarbeitung —
             diese Seite ist ihr einziger Ort. Das betrifft `Strategie und Seitenstruktur`
             und `Domain und Launch`; für beide ist keine eigene Seite vorgesehen (§10:
             `/leistung-domain-launch` steht auf Stufe 2).

             **Kein Satz ist verloren.** Die gekürzten `ausfuehrlich`-Blöcke stehen
             unverändert auf der jeweiligen Leistungsseite. */ ?>
    <ul class="leistungszeilen">
<?php foreach (Leistungszeilen::alle() as $zeile): ?>
      <li>
        <h3><?= Html::e($zeile['titel']) ?></h3>
<?php if ($zeile['ziel'] === null): ?>
<?php foreach ($zeile['ausfuehrlich'] as $satz): ?>
        <p><?= Html::e($satz) ?></p>
<?php endforeach; ?>
<?php else: ?>
        <p><?= Html::e($zeile['satz']) ?></p>
<?php endif; ?>
        <p class="marken"><?= Html::e(implode(' · ', $zeile['tags'])) ?></p>
<?php if ($zeile['ziel'] !== null): ?>
        <p><a class="textlink" href="<?= Html::e($zeile['ziel']) ?>"><?= Html::e($zeile['titel']) ?> im Einzelnen</a></p>
<?php endif; ?>
      </li>
<?php endforeach; ?>
    </ul>
  </div>
</section>

<?php /* Die Zusage — randlos dunkel, ein Satz. Sie steht nach den acht Zeilen und vor den
         sieben Entscheidungen: an der Stelle, an der aus der Aufzählung eine Aussage wird.
         Der Satz ist nicht neu — er stand als erster Satz im Vorspann und ist dorthin
         gezogen, wo er trägt. */ ?>
<section class="zusage">
  <div class="bahn">
    <p><?= Html::e(T::LEISTUNGEN_ZUSAGE) ?></p>
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

<?php /* Die drei Branchenseiten hatten null eingehende Verweise — sie standen nur in der
         Sitemap, und eine Seite, auf die nichts zeigt, wird als unwichtig gelesen.

         Sie stehen hier und nicht in der Hauptnavigation: §2 bindet dort sechs Punkte, und
         die Zeile ist am 13.08.2026 nachgemessen worden — sie traegt keinen siebten, ohne
         dass §1 wieder verletzt waere. Der Fussbereich ist nach §2a auf fuenf Spalten mit
         gebundenem Inhalt festgelegt.

         `/leistungen` ist der Navigationspunkt fuer genau diese Frage und damit von jeder
         Seite einen Klick entfernt. Der Widerspruch zur Anweisung „haeng sie in die
         Navigation" steht in `OFFENE_PRUEFUNGEN.md`. */ ?>
<section class="abschnitt" id="branchen">
  <div class="bahn">
    <h2>Drei Branchen haben eine eigene Seite.</h2>
    <p class="lede">Dort steht, welche Seiten ein Betrieb dieser Art braucht und was auf
    jede davon gehört — mit den Zahlen der jeweiligen Branche.</p>

    <ul class="leistungszeilen">
<?php foreach (Branchenseiten::alle() as $schluessel => $seite): ?>
      <li>
        <h3><a href="<?= Html::e(Branchenseiten::pfad($schluessel)) ?>"><?= Html::e((string) $seite['h1']) ?></a></h3>
        <p><?= Html::e((string) $seite['kurz']) ?></p>
      </li>
<?php endforeach; ?>
    </ul>
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
    <div class="handlungsfeld">
      <div class="handlungsfeld__text">
        <h2>Welche Website passt zu Ihrem Unternehmen?</h2>
      </div>

      <div class="handlungsfeld__handlung">
        <?= Ansicht::teil('partials/handlungsblock', [
            'auftragslage' => $auftragslage,
            'preishinweis' => Websitetexte::ABSCHLUSSHINWEIS . ' ' . $preishinweis,
            'zweitziel'    => '/ablauf',
            'zweittext'    => 'Ablauf ansehen',
            'dunkel'       => true,
        ]) ?>
      </div>
    </div>
  </div>
</section>
