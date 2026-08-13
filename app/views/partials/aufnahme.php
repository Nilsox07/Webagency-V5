<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/**
 * Eine **echte Aufnahme** aus dem eigenen Kundenbereich.
 *
 * ## Warum es dieses Bauteil neben `bildplatz.php` gibt
 *
 * `partials/bildplatz.php` zeichnet einen beschrifteten Rahmen mit
 * `[[SCREENSHOT-FEHLT]]` — ehrlich, solange es die Aufnahme nicht gibt, und von der
 * Startsperre §14a Bedingung 4 gesucht. Es bleibt unverändert für **Musterprojekte** und
 * das **Gründerfoto**: Dort ist das Bild der Beleg, und der Beleg fehlt noch (§5).
 *
 * **Für den Kundenbereich fehlt er nicht mehr.** `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4b: „Der
 * Grund ist seit dem 10.08.2026 entfallen: Der Kundenbereich ist gebaut. Es gibt eine echte
 * Oberfläche, von der sich eine echte Aufnahme machen lässt."
 *
 * ## Was auf den Aufnahmen zu sehen ist
 *
 * Die gebauten Seiten mit **Musterdaten** — `Mustermann`, ein erfundenes Angebot mit den
 * Zahlen aus `Preise::tabelle()`, drei offene Aufgaben. Keine echten Kundennamen, keine
 * realistischen Rechnungsnummern (§4b, „Was auf der Aufnahme zu sehen sein darf").
 *
 * ## Der Vermerk ist Pflicht und steht neben dem Bild
 *
 * `Musteransicht` ist gebunden. `WebsiteTest::testEinePortalansichtTraegtDenVermerk` prüft,
 * dass keine Seite eine `sartu-portal-*`-Aufnahme ohne ihn ausliefert.
 *
 * Er steht **unter** dem Bild, nicht darauf — dieselbe Entscheidung wie beim Aufmacher am
 * 13.08.2026: Ein Vermerk auf der Aufnahme verdeckt genau das, was sie zeigen soll.
 *
 * @var string $datei   Dateiname unter `/assets/bild/`
 * @var string $alt     was zu sehen ist — wird vorgelesen, beschreibt das Bild
 * @var int $breite
 * @var int $hoehe
 * @var string|null $marke  der gebundene Vermerk, `null` nur wo keine Oberfläche zu sehen ist
 */

$marke = $marke ?? null;

?>
<figure class="aufnahme">
  <img src="/assets/bild/<?= Html::e($datei) ?>"
       alt="<?= Html::e($alt) ?>"
       width="<?= (int) $breite ?>" height="<?= (int) $hoehe ?>"
       loading="lazy" decoding="async">
<?php if ($marke !== null): ?>
  <figcaption class="aufnahme__marke"><?= Html::e($marke) ?></figcaption>
<?php endif; ?>
</figure>
