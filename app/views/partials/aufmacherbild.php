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

/**
 * Die Basisflaeche als bilineare Flaeche — vier Ecken, und jeder Punkt darauf.
 *
 * Die Basis ist ein Trapez: hinten am Scharnier so breit wie der Deckel, vorn breiter,
 * weil sie vom Betrachter weg nach unten kippt. Eine Taste, die als gerades Rechteck
 * darauf gelegt wird, steht quer zur Flaeche und verrät die Zeichnung sofort.
 *
 * `punkt(u, v)` liefert deshalb zu jedem Anteil in Breite (`u`, links nach rechts) und
 * Tiefe (`v`, Scharnier nach vorn) den Punkt auf der Flaeche. Alles, was auf der Basis
 * liegt, wird damit gebaut — Tasten, Leertaste, Trackpad und die Vorderkante.
 */
$hintenLinks  = [92.0, 438.0];
$hintenRechts = [832.0, 462.0];
$vornLinks    = [10.0, 556.0];
$vornRechts   = [918.0, 584.0];

$punkt = static function (float $u, float $v) use ($hintenLinks, $hintenRechts, $vornLinks, $vornRechts): array {
    $hx = $hintenLinks[0] + $u * ($hintenRechts[0] - $hintenLinks[0]);
    $hy = $hintenLinks[1] + $u * ($hintenRechts[1] - $hintenLinks[1]);
    $vx = $vornLinks[0] + $u * ($vornRechts[0] - $vornLinks[0]);
    $vy = $vornLinks[1] + $u * ($vornRechts[1] - $vornLinks[1]);

    return [$hx + $v * ($vx - $hx), $hy + $v * ($vy - $hy)];
};

/** Ein Viereck auf der Flaeche, als Pfadangabe. */
$feld = static function (float $u1, float $v1, float $u2, float $v2) use ($punkt): string {
    $ecken = [$punkt($u1, $v1), $punkt($u2, $v1), $punkt($u2, $v2), $punkt($u1, $v2)];

    return 'M' . implode(' L', array_map(
        static fn (array $e): string => round($e[0], 1) . ' ' . round($e[1], 1),
        $ecken
    )) . ' Z';
};

$basisform = $feld(0.0, 0.0, 1.0, 1.0);

/*
 * Die Anteile stammen aus `design/geraet.html` und sind nicht geschaetzt.
 *
 * Dort ist die Basis 215 px hoch mit 22 px Innenabstand oben; `.tasten` ist 96 px hoch,
 * `.pad` folgt mit 16 px Abstand und ist 58 px hoch, `.kante` 11 px. In Anteilen der
 * Basistiefe: Tastenfeld 0,102–0,549 · Trackpad 0,623–0,893 · Kante ab 0,949.
 *
 * **Das Trackpad steht mittig.** Der Entwurf setzt `margin: 16px auto 0` — und ein
 * Trackpad, das erkennbar links der Mitte sitzt, liest sich als Zeichenfehler, nicht als
 * Perspektive. Der erste Bau hatte es nach links geschoben mit der Begruendung, das
 * angeschnittene Telefon verdecke die rechte Vorderflaeche. Nachgerechnet stimmt das
 * nicht: Die rechte Kante des mittigen Trackpads liegt bei x≈575, das Telefon beginnt
 * bei x=756.
 */
$tasten = [];
$reihen = 5;
$spalten = 14;

for ($reihe = 0; $reihe < $reihen; $reihe++) {
    $v1 = 0.120 + $reihe * 0.070;
    $v2 = $v1 + 0.055;

    for ($spalte = 0; $spalte < $spalten; $spalte++) {
        $u1 = 0.12 + $spalte * (0.76 / $spalten);
        $u2 = $u1 + (0.76 / $spalten) * 0.78;
        $tasten[] = $feld($u1, $v1, $u2, $v2);
    }
}

// Leertaste: im Entwurf 31 % Einzug je Seite, unterste Zeile des Tastenfelds.
$leertaste = $feld(0.337, 0.472, 0.663, 0.527);
// Trackpad: 30 % Breite, mittig, mit 16 px Abstand unter dem Tastenfeld.
$trackpad  = $feld(0.371, 0.623, 0.629, 0.893);

// Die Vorderkante ist eine eigene Flaeche unter der Basis — 14 Einheiten hoch.
$vk = [$punkt(0.0, 1.0), $punkt(1.0, 1.0)];
$vorderkante = sprintf(
    'M%.1f %.1f L%.1f %.1f L%.1f %.1f L%.1f %.1f Z',
    $vk[0][0], $vk[0][1], $vk[1][0], $vk[1][1],
    $vk[1][0], $vk[1][1] + 14, $vk[0][0], $vk[0][1] + 14
);

// Die Griffmulde in der Mitte der Vorderkante — im Entwurf 20 % breit, mittig.
$gm = [$punkt(0.40, 1.0), $punkt(0.60, 1.0)];
$griffmulde = sprintf(
    'M%.1f %.1f L%.1f %.1f L%.1f %.1f L%.1f %.1f Z',
    $gm[0][0], $gm[0][1] + 4, $gm[1][0], $gm[1][1] + 4,
    $gm[1][0], $gm[1][1] + 11, $gm[0][0], $gm[0][1] + 11
);

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
  <svg class="geraet__bild" viewBox="0 0 1000 760" role="img"
       aria-labelledby="geraet-titel" width="1000" height="760">
    <title id="geraet-titel">Der Kundenbereich mit einer Musteransicht: drei offene Aufgaben und der Projektstand.</title>

    <defs>
      <!-- Der Schirm als Trapez: Die linke Kante ist kuerzer als die rechte, dadurch
           weicht sie zurueck. Die Aufnahme wird auf dieselbe Form beschnitten und
           bekommt damit dieselbe Perspektive wie das Gehaeuse. -->
      <clipPath id="geraet-schirm">
        <path d="M116 106 L808 68 L808 442 L116 418 Z"/>
      </clipPath>
      <clipPath id="geraet-telefon">
        <rect x="762" y="470" width="146" height="256" rx="20"/>
      </clipPath>
      <!-- Alles, was auf der Basis liegt, wird an ihrer Flaeche beschnitten. Ohne den
           Beschnitt haengt eine Taste ueber der Kante, sobald das Raster nicht genau
           aufgeht. -->
      <clipPath id="geraet-basis">
        <path d="<?= Html::e($basisform) ?>"/>
      </clipPath>
      <!-- Der Verlauf der Basis faellt frueh ab, nicht gleichmaessig. Der Entwurf setzt
           `#33362f 0%, #242723 6%, #1a1c19 34%, #131512 74%, #0c0d0c 100%` — hell nur an
           der Scharnierkante, danach schnell dunkel. Ein gerader Verlauf von hell nach
           dunkel haelt die obere Haelfte so hell, dass die Tasten darin verschwinden;
           genau das war beim ersten Bau der Fall. -->
      <linearGradient id="geraet-sockelfarbe" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0" stop-color="var(--geraet-rahmen)"/>
        <stop offset=".34" stop-color="var(--geraet-sockel)"/>
        <stop offset="1" stop-color="var(--geraet-kerbe)"/>
      </linearGradient>
      <!-- Der Schattenkeil dort, wo Deckel und Basis zusammenstossen. Der Entwurf nennt
           ihn ausdruecklich: ohne ihn sieht die Basis aus, als schwebe sie. -->
      <linearGradient id="geraet-keil" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0" stop-color="var(--ink)" stop-opacity=".72"/>
        <stop offset="1" stop-color="var(--ink)" stop-opacity="0"/>
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

      <!-- **Die aufgeklappte Basis.** `design/geraet.html` nennt sie als groessten
           Einzeleffekt: „Fehlt sie, sieht das Geraet aus wie ein Bildschirm auf einem
           Stiel." Sie liegt flacher als der Deckel und laeuft nach vorn auseinander —
           hinten so breit wie er, vorn breiter. -->
      <path d="<?= Html::e($basisform) ?>" fill="url(#geraet-sockelfarbe)"
            stroke="var(--line-dark)" stroke-width="2" stroke-linejoin="round"/>

      <g clip-path="url(#geraet-basis)">
        <!-- Der Schattenkeil am Scharnier. -->
        <path d="M92 438 L832 462 L832 500 L92 476 Z" fill="url(#geraet-keil)"/>

        <!-- Tastenfeld: fuenf Reihen, vierzehn Tasten. Die Koordinaten sind in PHP
             ueber die Flaeche interpoliert, damit jede Taste in der Perspektive der
             Basis liegt statt als gerades Rechteck darauf. -->
<?php foreach ($tasten as $taste): ?>
        <path d="<?= Html::e($taste) ?>" fill="var(--geraet-taste)"/>
<?php endforeach; ?>

        <!-- Leertaste. -->
        <path d="<?= Html::e($leertaste) ?>" fill="var(--geraet-taste)"/>

        <!-- Trackpad: gleiche Flaeche, hellere Kante — im Entwurf ein eigener Verlauf
             mit `inset 0 1px 0`. -->
        <path d="<?= Html::e($trackpad) ?>" fill="var(--geraet-trackpad)"
              stroke="var(--line-dark)" stroke-width="1.5"/>
      </g>

      <!-- Vorderkante mit Griffmulde. Sie steht als eigene Flaeche unter der Basis und
           traegt oben eine helle Kante — sonst laeuft sie mit dem Sockel zusammen und
           das Geraet hat keine Vorderseite. -->
      <path d="<?= Html::e($vorderkante) ?>" fill="var(--geraet-sockel)"
            stroke="var(--geraet-taste)" stroke-width="1.5" stroke-linejoin="round"/>
      <path d="<?= Html::e($griffmulde) ?>" fill="var(--geraet-kerbe)"/>
    </g>

    <!-- Angeschnitten, wie im Entwurf: Das Telefon steht **vor** dem Laptop und ragt an
         der rechten unteren Ecke heraus. Es steht weit genug aussen, dass es die
         Arbeitsflaeche auf dem Schirm nicht verdeckt — die ist der Grund, warum das
         Geraet ueberhaupt dasteht. -->
    <g filter="url(#geraet-schatten-klein)">
      <rect x="756" y="464" width="158" height="268" rx="26"
            fill="var(--ink)" stroke="var(--line-dark)" stroke-width="2"/>
      <g clip-path="url(#geraet-telefon)">
        <image href="<?= Html::e($aufnahme) ?>" x="762" y="470" width="370" height="240"
               preserveAspectRatio="xMinYMin slice"/>
      </g>
    </g>
  </svg>
<?php endif; ?>

  <figcaption class="geraet__marke"><?= Html::e($marke) ?></figcaption>
</figure>
