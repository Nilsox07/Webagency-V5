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
    <?php /* `07_MARKE_UND_GESTALTUNG.md`: Kopfleiste traegt Zeichen + Wortmarke, ohne
             Zusatz, 34 px hoch. Bis zum 10.08.2026 stand hier das Wort als Text — die
             Logodateien lagen ausgeliefert unter /assets/bild/ und wurden von keiner
             Ansicht eingebunden.
             Die helle Fassung traegt die Wortmarke in --ink und gehoert auf hellen Grund;
             `alt` bleibt der Markenname, nicht „Logo" — Bildbeschreibungen werden
             vorgelesen. */ ?>
    <a class="seitenmarke" href="/">
      <img src="/assets/bild/sartu-logo-hell.svg" alt="SARTU" width="181" height="34">
    </a>

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
      <?php /* **Kein `aria-label`.** Bis zum 13.08.2026 stand hier `Menü öffnen`. Das ist
               falsch, sobald das Menü offen ist: Ein Screenreader liest dann „Menü öffnen,
               erweitert" — eine Aufforderung, die dem Zustand widerspricht.

               Ein `<details>` kann das nicht lösen, indem es umbenannt wird: Der Zustand
               steht ohne Skript nicht zur Verfügung, und §1 verlangt volle Bedienbarkeit
               ohne. Er muss auch nicht — der Browser gibt `summary` von sich aus
               `aria-expanded`, und das ist die Angabe, die den Zustand traegt.

               Uebrig bleibt der Name, und der lautet in beiden Zustaenden gleich: `Menü`.
               Ohne `aria-label` ist der sichtbare Text der Name — sie sind damit auch nicht
               mehr zwei verschiedene Dinge, was Sprachsteuerung sonst aushebelt. */ ?>
      <?php /* **Das Kreuz ist Teil des `summary`, kein zweiter Knopf.** Geändert am
               15.08.2026. Gemessen bei 390 px: Das offene Menü lag als `position: fixed;
               inset: 0` über Logo **und** Menütaste — mit der Tastatur schloss `Esc`, für
               einen Finger gab es keinen sichtbaren Weg zurück.

               Ein eigener Schliessknopf wäre der falsche Weg: Er bräuchte JavaScript, und
               §1 verlangt volle Bedienbarkeit ohne. Das `summary` schliesst das `details`
               von sich aus — es muss nur **über** dem Blatt liegen und sichtbar machen,
               dass es jetzt schliesst. Der Name bleibt in beiden Zuständen `Menü`; das
               Kreuz ist `aria-hidden` und trägt keine zweite Bezeichnung. */ ?>
      <summary><span class="menue__wort">Menü</span><span class="menue__kreuz" aria-hidden="true"></span></summary>
      <div class="menue__blatt">
        <?php /* **Die Hinterlegung ist eine echte Fläche, kein `::before`.** Ein Klick
                 daneben konnte vorher nicht greifen: Das Blatt liegt **innerhalb** des
                 `details`, und `menue.contains(ziel)` war deshalb bei jedem Klick wahr.
                 Jetzt gibt es ein Element, auf das der Klick fällt — `menue.js` erkennt es
                 an `data-hinterlegung`. Ohne Skript schliesst weiterhin das Kreuz. */ ?>
        <div class="menue__hinterlegung" data-hinterlegung aria-hidden="true"></div>

        <div class="menue__inhalt">
          <ul>
<?php foreach (Websitetexte::NAVIGATION as $ziel => $beschriftung): ?>
            <li><a href="<?= Html::e($ziel) ?>"><?= Html::e($beschriftung) ?></a></li>
<?php endforeach; ?>
            <li><a href="/kontakt">Kontakt</a></li>
          </ul>
          <a class="knopf knopf--breit" href="/briefing"><?= Html::e(Auftragslage::KNOPF) ?></a>
        </div>
      </div>
    </details>
  </div>
</header>
