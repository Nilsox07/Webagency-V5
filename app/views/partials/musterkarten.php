<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Html;
use Sartu\Services\Musterprojekte;

/**
 * Die drei Karten aus Sektion 8 — `10_WEBSITE_SARTU.md` §8, Aufbau aus
 * `design/startseite.html`, `id="muster"`.
 *
 * Je Karte: die gebundene Fahne, ein beschrifteter Bildplatz, der Gattungsname und die vier
 * Zeilen Ausgangslage · Empfohlen · Seitenstruktur · Sie liefern.
 *
 * ## Warum die vier Zeilen ein `<dl>` sind und keine Aufzählung
 *
 * Es sind **Paare** — Beschriftung und Angabe. Eine `<ul>` mit „Ausgangslage: …" bildet das
 * nur nach; das Vorleseprogramm liest dann einen Satz statt zwei Felder. Und die Beschriftung
 * liesse sich nicht getrennt setzen, ohne ein `<span>` zu erfinden, das nichts bedeutet.
 *
 * ## Der Bildplatz ist Absicht, nicht Rest
 *
 * §8, Stufe 1: „Sektion mit ehrlich beschriftetem Bildplatz. Der Inhalt trägt sie auch ohne
 * Bild." Solange keine Beispielseite gebaut ist, ist **kein nachgebauter Bildschirm** erlaubt
 * — die Sperre steht in §8 und in `17_SEITEN_SARTU.md` §4a doppelt.
 *
 * Der Platz trägt `[[SCREENSHOT-FEHLT]]` aus `partials/bildplatz.php`. Seit dem 13.08.2026
 * sucht `Platzhalterpruefung` diese Markierung wirklich, und `bin/startklar.php` bricht daran
 * ab — vorher war die Zusage im Kommentar von `bildplatz.php` ohne Deckung.
 */

?>
<div class="muster">
<?php foreach (Musterprojekte::alle() as $schluessel => $projekt): ?>
  <article class="musterkarte hebt">
    <p class="marke"><?= Html::e(Musterprojekte::FAHNE) ?></p>

    <?= Ansicht::teil('partials/bildplatz', [
        'name'   => Musterprojekte::bildname($schluessel),
        'breite' => 1280,
        'hoehe'  => 800,
        'satz'   => Musterprojekte::bildsatz($schluessel),
    ]) ?>

    <h3><?= Html::e($projekt['gattung']) ?></h3>
    <?php /* Der Umfang steht seit dem 13.08.2026 hier und nicht mehr im Bildplatz —
             dort stand er hinter der Gattung, und die Gattung stand einen Zeilenabstand
             weiter unten schon als Ueberschrift. */ ?>
    <p class="musterkarte__umfang"><?= Html::e(Musterprojekte::umfangszeile($schluessel)) ?></p>

    <?php /* **Ein Satz je Karte, nicht vier Zeilen** — Arbeitsteilung vom 14.08.2026.
             Die Karte hier und der Fall auf `/musterprojekte` trugen dieselben vier
             Angaben; zusammen waren das 258 Woerter, die zweimal dasselbe sagten.
             Die Karte zeigt jetzt die Ausgangslage und schickt weiter, die
             Uebersichtsseite traegt die Ausarbeitung. **Keine Aussage ist verloren** —
             Empfehlung, Struktur und was der Kunde liefert stehen unveraendert dort.

             Das weicht von `10_WEBSITE_SARTU.md` §8 ab, der je Karte vier Angaben
             bindet. Gemeldet in `OFFENE_PRUEFUNGEN.md`, nicht stillschweigend. */ ?>
    <p class="musterkarte__satz"><?= Html::e((string) $projekt['ausgangslage']) ?></p>
  </article>
<?php endforeach; ?>
</div>
