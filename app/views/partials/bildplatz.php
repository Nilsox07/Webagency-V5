<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/**
 * Ein beschrifteter Bildplatz — Auftrag §6, „Bildmaterial, 15 Plätze".
 *
 * > „Gekennzeichneter Bildplatz: 2 px gestrichelt, Monoschrift-Zeile, ein Satz dazu, was
 * > dort später steht."
 *
 * **Warum überhaupt ein sichtbarer Platz und kein Weglassen.** Zwei Regeln stehen hier
 * nebeneinander, und sie widersprechen sich nicht:
 *
 * `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5 verbietet den leeren Rahmen **an einer
 * Vertrauensstelle** — Gründerfoto, Referenz, Musterprojekt. Seit §4d gilt dort statt dessen
 * ein **gekennzeichneter** Platz, an dem die Startsperre die Veröffentlichung anhält.
 *
 * An den übrigen Stellen ist das Bild Erläuterung, kein Beleg. Ein gekennzeichneter Platz
 * mit einem Satz ist dort ehrlicher als ein stillschweigend engerer Text — und er ist
 * auffindbar, wenn die Aufnahmen kommen.
 *
 * ## Die Marke steht im Markup, nicht auf der Seite — geändert am 13.08.2026
 *
 * Bis dahin las der **Besucher** `[[SCREENSHOT-FEHLT]] sartu-muster-malerbetrieb.webp ·
 * 1280 × 800`, dreimal untereinander. Dateiname und Pixelmaße sind Bauwissen; auf der Seite
 * stehen sie niemandem zur Verfügung, der etwas damit anfangen kann.
 *
 * **Die Markierung muss trotzdem im ausgelieferten Markup stehen** — `10_WEBSITE_SARTU.md`
 * §5 Bedingung 4, und `Platzhalterpruefung` sucht sie dort. Sie steht deshalb als
 * `data-fehlt`: nicht sichtbar, nicht vorgelesen, maschinell auffindbar.
 *
 * Sichtbar bleibt, was der abgenommene Entwurf an dieser Stelle schreibt
 * (`design/startseite.html`, `.sample-shot`): **`Platz für Ansicht`** und ein Satz, was dort
 * hinkommt.
 *
 * ## Das Seitenverhältnis gehört jetzt dazu
 *
 * Ohne `aspect-ratio` nimmt der Platz die Höhe seines Textes an — gemessen am 13.08.2026:
 * einer 427 px hoch, wo das Bild 213 px braucht, andere 42 px. **Sobald die Aufnahmen kommen,
 * springt das Layout an jeder dieser Stellen.** Der Platz trägt deshalb das Verhältnis, das
 * sein späteres Bild hat, und nimmt heute schon den Raum ein, den es später braucht.
 *
 * **Das Verhältnis steht als `data-verhaeltnis`, nicht als `style`.** Die eigene CSP führt
 * `style-src 'self'` ohne `unsafe-inline`; ein Inline-Attribut wäre wirkungslos. Derselbe
 * Fehler hatte am 13.08.2026 schon einmal 29 px weissen Rand über der Portalleiste gekostet.
 * `Bildmasse::verhaeltnis()` kürzt den Bruch, `website.css` führt je Verhältnis eine Regel.
 *
 * @var string $name    Dateiname ohne Endung
 * @var int $breite     die Breite des späteren Bildes
 * @var int $hoehe      die Höhe des späteren Bildes
 * @var string $satz    was dort hinkommt — für den Leser
 * @var string|null $marke
 */

$marke = $marke ?? null;

?>
<figure class="bildplatz"
        data-verhaeltnis="<?= Html::e(\Sartu\Helpers\Bildmasse::verhaeltnis((int) $breite, (int) $hoehe)) ?>"
        data-fehlt="[[SCREENSHOT-FEHLT]] <?= Html::e($name) ?>.webp · <?= (int) $breite ?> × <?= (int) $hoehe ?>">
<?php if ($marke !== null): ?>
  <p class="bildplatz__marke"><?= Html::e($marke) ?></p>
<?php endif; ?>
  <p class="bildplatz__kennung">Platz für Ansicht</p>
  <figcaption><?= Html::e($satz) ?></figcaption>
</figure>
