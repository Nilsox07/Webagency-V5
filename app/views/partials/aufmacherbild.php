<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/**
 * Das Gerät im Aufmacher — `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4b, entschieden 10.08.2026.
 *
 * ## Was hier steht und was nicht
 *
 * **Der Rahmen ist selbst gezeichnet.** Laptop und angeschnittenes Telefon entstehen aus
 * Flächen, Kanten und einem Inline-SVG für den Schatten unter dem Gerät. Keine gekaufte
 * Vorlage, keine heruntergeladene, **kein externer Abruf** — die Regel gilt auch für ein Bild,
 * das nur hübsch ist.
 *
 * **Auf dem Bildschirm steht eine echte Aufnahme.** `sartu-kundenbereich-muster.webp` zeigt
 * den gebauten Kundenbereich mit Musterdaten. Der abgenommene Entwurf zeichnet den
 * Bildschirminhalt an dieser Stelle mit Balken und Platzhalterzeilen nach;
 * `10_WEBSITE_SARTU.md` §8 verbietet genau das. **Übernommen ist der Rahmen, nicht der
 * nachgebaute Inhalt.**
 *
 * ## Warum hier kein Bildplatz mehr steht
 *
 * `partials/bildplatz.php` bleibt unverändert und gilt weiter für **Musterprojekte** und das
 * **Gründerfoto**. Dort ist das Bild der Beleg, und ein leerer Rahmen an einer
 * Vertrauensstelle ist unzulässig (§5). Im Aufmacher ist das Bild kein Beleg, sondern das
 * eigene Produkt — und das gibt es.
 *
 * ## Der Vermerk bleibt
 *
 * `Musteransicht` ist gebunden (§5 Sektion 1, „Visual rechts mit Kennzeichen"). Er steht **am
 * Bild**, nicht darunter im Fließtext: Wer die Aufnahme sieht, soll im selben Blick lesen,
 * dass die Daten erfunden sind.
 *
 * @var string $marke der gebundene Vermerk
 */

?>
<figure class="geraet">
  <div class="geraet__laptop">
    <div class="geraet__deckel">
      <div class="geraet__schirm">
        <img src="/assets/bild/sartu-kundenbereich-muster.webp"
             alt="Der Kundenbereich mit einer Musteransicht: drei offene Aufgaben und der Projektstand."
             width="1600" height="1040" loading="eager" decoding="async">
      </div>
    </div>
    <div class="geraet__sockel" aria-hidden="true"></div>
  </div>

  <div class="geraet__telefon" aria-hidden="true">
    <div class="geraet__schirm">
      <img src="/assets/bild/sartu-kundenbereich-muster.webp" alt="" width="1600" height="1040">
    </div>
  </div>

  <figcaption class="geraet__marke"><?= Html::e($marke) ?></figcaption>
</figure>
