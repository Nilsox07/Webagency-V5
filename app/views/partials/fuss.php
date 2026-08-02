<?php

declare(strict_types=1);

/**
 * Der Fussbereich.
 *
 * Hier stand bis zum 02.08.2026 ein Datenbankzugriff, um den Firmennamen aus
 * `operator_settings` zu ziehen — samt `try/catch`, weil die Ansicht selbst nicht sicher
 * sein konnte, dass er gelingt. Genau das verbietet §1.3 und der Kopf von `Ansicht`:
 * „Datenbankzugriff schon gar nicht. Eine Ansicht bekommt fertige Werte und gibt sie aus."
 * Er lief auf JEDER Antwort, auch auf 404, 419 und der Wartungsseite.
 *
 * Die Angaben aus §1.4a gehoeren in den Fussbereich der oeffentlichen Website — und die
 * entsteht nach Stufe B (`REIHENFOLGE.md`, „Zwei Livegaenge"). Sie werden dann als Wert
 * hereingereicht, nicht hier geholt.
 */

?>
<footer class="fussband">
  <div class="bahn">
    <p><a href="/impressum">Impressum</a> · <a href="/datenschutz">Datenschutz</a> · <a href="/agb">AGB</a></p>
  </div>
</footer>
