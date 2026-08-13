<?php

declare(strict_types=1);

use Sartu\Helpers\Html;
use Sartu\Services\Websitetexte;

/**
 * Der Fußbereich der öffentlichen Website — Website-Lastenheft §4, ausdrücklich „final".
 *
 * Fünf Spalten, Reihenfolge verbindlich. Spalte 1 und 5 stehen hier, die Spalten 2 bis 4
 * kommen aus `Websitetexte::fussspalten()` — dieselben Beschriftungen wie in der Navigation,
 * aus einer Quelle.
 *
 * **`AGB` fehlt bewusst.** §4: „AGB nur, wenn anwaltlich final." §14 verschärft das noch:
 * „Sonst **gar nicht** verlinken und `noindex`." Der Text steht in `legal_texts` auf
 * `entwurf`, also gibt es hier keinen Verweis darauf.
 *
 * **Verboten im Fußbereich** (§4, wörtlich): Ortslisten, Keyword-Linklisten, Social-Icons
 * ohne echte gepflegte Profile, „Made with"-Hinweise. Nichts davon steht hier.
 *
 * @var bool $kleinunternehmer
 */

?>
<footer class="seitenfuss">
  <div class="bahn seitenfuss__spalten">
    <div class="seitenfuss__marke">
      <?php /* Fussbereich, Spalte: Zeichen + Wortmarke, 30 px. Dunkler Grund, deshalb
               die dunkle Fassung mit weisser Wortmarke. */ ?>
      <p class="seitenmarke"><img src="/assets/bild/sartu-logo-dunkel.svg" alt="SARTU" width="160" height="30"></p>
      <p><?= Html::e(Websitetexte::KURZPOSITIONIERUNG) ?></p>
    </div>

<?php foreach (Websitetexte::fussspalten() as $ueberschrift => $punkte): ?>
    <nav aria-label="<?= Html::e($ueberschrift) ?>">
      <h2><?= Html::e($ueberschrift) ?></h2>
      <ul>
<?php foreach ($punkte as $ziel => $beschriftung): ?>
        <li><a href="<?= Html::e($ziel) ?>"><?= Html::e($beschriftung) ?></a></li>
<?php endforeach; ?>
      </ul>
    </nav>
<?php endforeach; ?>

    <nav aria-label="Rechtliches">
      <h2>Rechtliches</h2>
      <ul>
        <li><a href="/impressum">Impressum</a></li>
        <li><a href="/datenschutz">Datenschutz</a></li>
      </ul>
    </nav>
  </div>

  <?php /* Die Wortmarke gross am Fuss, unten angeschnitten —
           `design/startseite.html` Zeile 1165. Der Entwurf begruendet, warum dort die
           **Wortmarke** steht und nicht das Zeichen: „Das Zeichen ist ein kompakter
           Koerper; angeschnitten wirkt er beschaedigt, nicht fortgesetzt."

           Dieselbe Zeichnung wie in `sartu-logo-dunkel.svg`, hier inline, weil sie
           `currentColor` braucht: Sie steht eine Spur heller als der Grund, nicht in
           einer eigenen Farbe. `aria-hidden` — die Marke steht auf derselben Seite
           schon zweimal als Text. */ ?>
  <div class="bahn wortriese" aria-hidden="true">
    <svg class="wortriese" viewBox="98 61.5 167.9 25.3"><path fill="currentColor" d="m120 71.1h-12.7c-1.2 0-2.1-0.6-2.1-1.7s0.9-1.8 2.1-1.8h18.8v-6.1h-19.8c-4.7 0-8.3 3.2-8.3 7.9s3.7 7.5 8.3 7.5h11.9c1.4 0 2.4 0.8 2.4 2s-1 2-2.4 2h-19.6v5.9h21.1c4.9-0.1 7.9-3.2 7.9-7.9 0-4.8-3.1-7.8-7.6-7.8zm22.3-9.6-13.6 24.8h7.8l2.3-3.8h15.4l1.8 3.8h8.6l-14-24.8h-8.3zm0 15.2 4-7.9 4.1 7.9h-8.1zm55.4-6.3c0-4.8-3.2-8.8-8.9-8.8h-21.9v24.7h7.8v-7.4h7.3l7 7.4h9.3l-7.2-7.7c3.8-1 6.6-3.9 6.6-8.2zm-10.6 2.8h-12.2v-5.7h11.9c1.9 0 3.1 1.2 3.1 2.9s-1.1 2.8-2.8 2.8zm13.3-5.7h11v18.8h7v-18.8h11.6v-5.9h-29.7l0.1 5.9zm57.5-6v14.3c0 2.8-1.2 4.7-4.2 4.7h-7.5c-2.3 0-4-1.9-4-4.3l0.1-14.7h-7.2v15.1c0 5.6 4.1 9.7 9.4 9.7h10.5c5.9 0 10.9-3.4 10.9-9.7l-0.1-15.1h-7.9z"/></svg>
  </div>

  <div class="bahn seitenfuss__zeile">
    <p>© 2026 SARTU</p>
    <p><?= Html::e(Websitetexte::preishinweis($kleinunternehmer ?? false)) ?></p>
  </div>
</footer>
