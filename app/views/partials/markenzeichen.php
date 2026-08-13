<?php

declare(strict_types=1);

/**
 * Die Bildmarke im Dokumentkopf — `16_SEO_GEO_SARTU.md`, „Root-Dateien":
 * „`/favicon.ico` samt PNG-Größen".
 *
 * **Bis zum 13.08.2026 gab es weder die Dateien noch den Verweis.** Kein Layout trug ein
 * `icon`, und `public/favicon.ico` lag nicht da — der Browser fragt die Adresse trotzdem an
 * und bekam die 404-Seite.
 *
 * ## Warum diese vier Zeilen und nicht mehr
 *
 * | Zeile | Wofür |
 * |---|---|
 * | `favicon.ico`, `sizes="any"` | Der Behälter mit 16, 32 und 48 px. Ältere Browser fragen die Adresse ohnehin ab, ob sie verlinkt ist oder nicht |
 * | die beiden PNG | Moderne Browser nehmen die passende Größe, statt eine zu skalieren |
 * | `apple-touch-icon` | 180 px, die Größe, die iOS für den Startbildschirm verlangt |
 *
 * **Kein `manifest.json`, kein `theme-color`, keine acht weiteren Größen.** Das wäre eine
 * Webanwendung zum Installieren; SARTU ist eine Website.
 *
 * ## Woher die Bilder stammen
 *
 * Aus `sartu-mark.svg`, der Datei, die schon im Repo liegt — auf `--ink` gesetzt, weil eine
 * Lime-Fläche in einer Browserleiste unbekannten Grundes verschwinden kann (Farbsystem
 * Fassung 3: 1,39 : 1 gegen hell). Erzeugt, nicht nachgezeichnet: Ein Favicon, das die
 * Marke leicht anders zeichnet, ist eine zweite Marke.
 *
 * **Diese Datei steht in jedem Layout** — auch im Kunden- und Adminbereich. Ein Bereich
 * ohne Zeichen ist im Tabstreifen nicht wiederzufinden.
 */

?>
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" type="image/png" href="/assets/bild/sartu-favicon-32.png" sizes="32x32">
<link rel="icon" type="image/png" href="/assets/bild/sartu-favicon-16.png" sizes="16x16">
<link rel="apple-touch-icon" href="/assets/bild/sartu-favicon-180.png">
