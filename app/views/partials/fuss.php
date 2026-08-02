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
 * Die Angaben aus §1.4a gehoeren in den Fussbereich der oeffentlichen Website — der steht
 * seit Stufe B in `partials/websitefuss`. Dieser hier traegt nur noch die Seiten, die kein
 * Website-Layout haben: Einrichtung, Anmeldung, Fehlerseiten.
 *
 * **`/agb` steht bewusst nicht mehr da.** Website-Lastenheft §14: „Nur live und verlinkt,
 * wenn anwaltlich final. Sonst **gar nicht** verlinken und `noindex`." Der Text steht in
 * `legal_texts` auf `entwurf`, also liefert die Route 404 — und ein Verweis auf eine 404 ist
 * ein toter Verweis (§17).
 */

?>
<footer class="fussband">
  <div class="bahn">
    <p><a href="/impressum">Impressum</a> · <a href="/datenschutz">Datenschutz</a></p>
  </div>
</footer>
