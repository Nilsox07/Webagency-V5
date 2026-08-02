<?php

declare(strict_types=1);

use Sartu\Helpers\Html;
use Sartu\Services\Auftragslage;
use Sartu\Services\Websitetexte;

/**
 * Die Kopfzeile der öffentlichen Website — Website-Lastenheft §3 (Verhalten) und §5b (Punkte).
 *
 * **§3 ist für die Punkteliste abgelöst.** Sie steht in `Websitetexte::NAVIGATION`, an einer
 * Stelle. §3 behält nur Verhalten und Maße.
 *
 * **Das mobile Menü ist ein `<details>`.** §3 verlangt Vollbild-Overlay, Schließen per X,
 * `Esc` und Klick außerhalb, dazu Fokus im Overlay. Ein `<details>` liefert Öffnen,
 * Schließen und die Tastaturbedienung vom Browser — ohne eine Zeile JavaScript, und §1
 * verlangt volle Nutzbarkeit ohne.
 *
 * Was ein `<details>` nicht kann, ist die Fokusfalle. Sie kommt seit dem 02.08.2026 aus
 * `/public/assets/js/menue.js` und **fügt nur hinzu**: Fällt das Skript aus, bleibt das
 * Menü vollständig bedienbar. Der Layoutkopf begründet, warum das die CSP nicht berührt.
 *
 * **Ob `Esc` wirklich schließt, ist eine Messung, keine Behauptung** — sie steht in
 * `OFFENE_PRUEFUNGEN.md` und wird im Browser nachgeholt, nicht hier zugesichert.
 *
 * @var string $pfad
 */

?>
<header class="seitenkopf">
  <div class="bahn seitenkopf__reihe">
    <a class="wortmarke" href="/">SARTU</a>

    <nav class="hauptnavigation" aria-label="Hauptnavigation">
      <ul>
<?php foreach (Websitetexte::NAVIGATION as $ziel => $beschriftung): ?>
        <li><a href="<?= Html::e($ziel) ?>"<?= $ziel === $pfad ? ' aria-current="page"' : '' ?>><?= Html::e($beschriftung) ?></a></li>
<?php endforeach; ?>
      </ul>
    </nav>

    <div class="seitenkopf__handlung">
      <a class="textlink" href="/kontakt">Kontakt</a>
      <a class="knopf" href="/briefing"><?= Html::e(Auftragslage::KNOPF) ?></a>
    </div>

    <details class="menue">
      <summary aria-label="Menü öffnen">Menü</summary>
      <div class="menue__blatt">
        <ul>
<?php foreach (Websitetexte::NAVIGATION as $ziel => $beschriftung): ?>
          <li><a href="<?= Html::e($ziel) ?>"><?= Html::e($beschriftung) ?></a></li>
<?php endforeach; ?>
          <li><a href="/kontakt">Kontakt</a></li>
        </ul>
        <a class="knopf knopf--breit" href="/briefing"><?= Html::e(Auftragslage::KNOPF) ?></a>
      </div>
    </details>
  </div>
</header>
