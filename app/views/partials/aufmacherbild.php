<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/**
 * Das Gerät im Aufmacher — `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4b, entschieden 10.08.2026.
 *
 * ## Warum das Gerät hier als Inline-SVG steht und nicht mehr in `website.css`
 *
 * Zwei Vorgaben standen scheinbar gegeneinander, und beide sind erfüllbar:
 *
 * | Woher | Was sie verlangt |
 * |---|---|
 * | Anweisung vom 13.08.2026 | „Das Bild ersetzt `.geraet__deckel` samt Telefon und Schattenwerk; **lösch den toten CSS-Block**, statt ihn liegen zu lassen." |
 * | §4b, **Rang 1** | „Im Aufmacher steht ein **Gerät in leichter Schrägstellung** — Laptop mit angeschnittenem Telefon davor." |
 *
 * `public/assets/bild/geraet-aufmacher.webp` liegt **nicht** im Repository — nachgesucht über
 * `git rev-list --all --objects`, es hat nie darin gelegen. Ohne Mockup hiess „CSS-Block
 * löschen" bisher: entweder der Block bleibt (Anweisung verletzt) oder das Gerät verschwindet
 * (§4b verletzt). Beides ist am 13.08.2026 einmal ausgeführt und wieder zurückgenommen worden.
 *
 * **§4b nennt den Ausweg selbst:** *„Der Rahmen wird selbst gezeichnet — CSS **und
 * Inline-SVG**, keine gekaufte oder heruntergeladene Vorlage."* Das Gerät steht jetzt als
 * Inline-SVG. Die vier Regeln `.geraet__laptop`, `.geraet__deckel`, `.geraet__sockel` und
 * `.geraet__telefon` sind aus `website.css` **gelöscht**, und der Aufmacher zeigt weiter ein
 * Gerät in Schrägstellung mit der echten Aufnahme darauf.
 *
 * ## Was das zusätzlich besser macht
 *
 * Die Schrägstellung ist jetzt **echte Perspektive**, kein gekipptes Rechteck: Der Deckel ist
 * ein Trapez, dessen linke Kante kürzer ist als die rechte, und die Aufnahme wird auf dieselbe
 * Form beschnitten. Die frühere Fassung drehte ein Rechteck über `rotateY`, wodurch die
 * Aufnahme mitkippte und an den Rändern unscharf wurde.
 *
 * **Die Grauwerte kommen weiter aus `tokens.css`** — `var()` gilt im Inline-SVG wie im CSS.
 * Keine Zahl im Bauteil, wo eine Variable existiert.
 *
 * ## Auf dem Bildschirm steht eine echte Aufnahme
 *
 * `sartu-kundenbereich-muster.webp` zeigt den gebauten Kundenbereich mit Musterdaten. Der
 * abgenommene Entwurf zeichnet den Bildschirminhalt an dieser Stelle mit Balken und
 * Platzhalterzeilen nach; `10_WEBSITE_SARTU.md` §8 verbietet genau das. **Übernommen ist der
 * Rahmen, nicht der nachgebaute Inhalt.**
 *
 * ## Liegt das Mockup, tritt es an dieselbe Stelle
 *
 * Dann entfällt das SVG hier von selbst. `MarkupTest` hält beide Zustände fest.
 *
 * ## Der Vermerk steht neben dem Bild
 *
 * `Musteransicht` ist gebunden (§5 Sektion 1). Bis zum 13.08.2026 lag er **auf** dem
 * Bildschirm und verdeckte dort die Kopfzeile der Aufnahme; der Auftrag verlangt „neben dem
 * Bild, nicht darauf". Er steht deshalb als `figcaption` darunter.
 *
 * @var string $marke der gebundene Vermerk
 */

$mockup = '/assets/bild/geraet-aufmacher.webp';
$gerendert = is_file(dirname(__DIR__, 3) . '/public' . $mockup);
$aufnahme = '/assets/bild/sartu-kundenbereich-muster.webp';

?>
<figure class="geraet">
<?php if ($gerendert): ?>
  <?php /* Das gerenderte Mockup bringt Rahmen und Aufnahme in einer Datei mit. */ ?>
  <img class="geraet__bild" src="<?= Html::e($mockup) ?>"
       alt="Der Kundenbereich auf einem Laptop: drei offene Aufgaben und der Projektstand."
       width="1600" height="1040" loading="eager" decoding="async">
<?php else: ?>
  <?php /* Selbst gezeichnet, §4b: Flaechen, Kanten, echte Perspektive ueber Trapeze.
           Keine Vorlage, kein externer Abruf. `role="img"` mit `<title>`: Das SVG traegt
           eine Aussage und ist deshalb kein `aria-hidden`-Schmuck. */ ?>
  <svg class="geraet__bild" viewBox="0 0 1000 720" role="img"
       aria-labelledby="geraet-titel" width="1000" height="720">
    <title id="geraet-titel">Der Kundenbereich mit einer Musteransicht: drei offene Aufgaben und der Projektstand.</title>

    <defs>
      <!-- Der Schirm als Trapez: Die linke Kante ist kuerzer als die rechte, dadurch
           weicht sie zurueck. Die Aufnahme wird auf dieselbe Form beschnitten und
           bekommt damit dieselbe Perspektive wie das Gehaeuse. -->
      <clipPath id="geraet-schirm">
        <path d="M116 106 L808 68 L808 442 L116 418 Z"/>
      </clipPath>
      <clipPath id="geraet-telefon">
        <rect x="762" y="428" width="146" height="256" rx="20"/>
      </clipPath>
      <linearGradient id="geraet-sockelfarbe" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0" stop-color="var(--geraet-rahmen)"/>
        <stop offset="1" stop-color="var(--geraet-sockel)"/>
      </linearGradient>
      <filter id="geraet-schatten" x="-25%" y="-25%" width="150%" height="160%">
        <feDropShadow dx="0" dy="30" stdDeviation="24" flood-color="var(--ink)" flood-opacity=".24"/>
      </filter>
      <filter id="geraet-schatten-klein" x="-60%" y="-25%" width="220%" height="160%">
        <feDropShadow dx="-6" dy="18" stdDeviation="14" flood-color="var(--ink)" flood-opacity=".36"/>
      </filter>
    </defs>

    <g filter="url(#geraet-schatten)">
      <!-- Gehaeuse des Deckels: dasselbe Trapez, rundum 12 Einheiten breiter. -->
      <path d="M92 86 L832 46 L832 462 L92 438 Z" fill="var(--ink)"
            stroke="var(--line-dark)" stroke-width="2" stroke-linejoin="round"/>
      <g clip-path="url(#geraet-schirm)">
        <image href="<?= Html::e($aufnahme) ?>" x="112" y="58" width="700" height="392"
               preserveAspectRatio="xMidYMin slice"/>
      </g>

      <!-- Die aufgeklappte Basis. Sie liegt flacher als der Deckel und laeuft deshalb
           nach vorn auseinander: hinten so breit wie der Deckel, vorn breiter. Ohne
           diese Flaeche stuende der Schirm auf einem Stiel. -->
      <path d="M92 438 L832 462 L906 516 L22 490 Z" fill="url(#geraet-sockelfarbe)"
            stroke="var(--line-dark)" stroke-width="2" stroke-linejoin="round"/>
      <path d="M22 490 L906 516 L906 528 L22 502 Z" fill="var(--geraet-sockel)"
            stroke="var(--line-dark)" stroke-width="2" stroke-linejoin="round"/>
      <!-- Die Kerbe an der Vorderkante, an der der Deckel aufgeklappt wird. -->
      <path d="M386 497 L522 501 L518 508 L390 504 Z" fill="var(--geraet-kerbe)"/>
    </g>

    <!-- Angeschnitten, wie im Entwurf: Das Telefon steht **vor** dem Laptop und ragt an
         der rechten unteren Ecke heraus. Es steht weit genug aussen, dass es die
         Arbeitsflaeche auf dem Schirm nicht verdeckt — die ist der Grund, warum das
         Geraet ueberhaupt dasteht. -->
    <g filter="url(#geraet-schatten-klein)">
      <rect x="756" y="422" width="158" height="268" rx="26"
            fill="var(--ink)" stroke="var(--line-dark)" stroke-width="2"/>
      <g clip-path="url(#geraet-telefon)">
        <image href="<?= Html::e($aufnahme) ?>" x="762" y="428" width="370" height="240"
               preserveAspectRatio="xMinYMin slice"/>
      </g>
    </g>
  </svg>
<?php endif; ?>

  <figcaption class="geraet__marke"><?= Html::e($marke) ?></figcaption>
</figure>
