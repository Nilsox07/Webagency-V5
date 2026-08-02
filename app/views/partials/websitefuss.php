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
      <p class="wortmarke">SARTU</p>
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

  <div class="bahn seitenfuss__zeile">
    <p>© 2026 SARTU</p>
    <p><?= Html::e(Websitetexte::preishinweis($kleinunternehmer ?? false)) ?></p>
  </div>
</footer>
