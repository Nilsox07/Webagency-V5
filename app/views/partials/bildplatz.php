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
 * Vertrauensstelle** — Gründerfoto, Referenz, Musterprojekt. Dort trägt das Bild den Beleg,
 * und ein leerer Rahmen sagt „hier fehlt der Beleg". Deshalb entfallen die Sektionen 6 und 8
 * der Startseite ganz.
 *
 * An den übrigen Stellen ist das Bild Erläuterung, kein Beleg. Ein gekennzeichneter Platz
 * mit Maßangabe und einem Satz ist dort ehrlicher als ein stillschweigend engerer Text —
 * und er ist auffindbar, wenn die Aufnahmen kommen.
 *
 * **`[[SCREENSHOT-FEHLT]]` steht bewusst im Markup.** Die Startsperre §14a Bedingung 4 sucht
 * genau diese Markierung und bricht die produktive Veröffentlichung ab. Eine freie
 * Formulierung wie „TODO" würde sie nicht finden.
 *
 * @var string $name
 * @var string $masse
 * @var string $satz
 * @var string|null $marke
 */

?>
<figure class="bildplatz">
<?php if (($marke ?? null) !== null): ?>
  <p class="bildplatz__marke"><?= Html::e($marke) ?></p>
<?php endif; ?>
  <p class="bildplatz__kennung">[[SCREENSHOT-FEHLT]] <?= Html::e($name) ?>.webp · <?= Html::e($masse) ?></p>
  <figcaption><?= Html::e($satz) ?></figcaption>
</figure>
