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
 * Schließen, `Esc` und die Tastaturbedienung vom Browser — ohne eine Zeile JavaScript, und
 * §1 verlangt volle Nutzbarkeit ohne. Was ein `<details>` nicht kann, ist die Fokusfalle;
 * das steht als offener Punkt in `OFFENE_PRUEFUNGEN.md`.
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
