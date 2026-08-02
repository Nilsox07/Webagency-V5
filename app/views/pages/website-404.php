<?php

declare(strict_types=1);

/**
 * Die 404-Seite der öffentlichen Website — Website-Lastenheft §14.
 *
 * Wortlaut gebunden: H1 `Diese Seite gibt es nicht.`, Text „Vielleicht wurde die Adresse
 * geändert oder eine alte Seite ist umgezogen.", vier Verweise, echter 404-Status, `noindex`.
 *
 * **Keine Suche, kein Formular, kein Bild.** Wer hier landet, hat sich vertan oder folgt
 * einem alten Verweis — er braucht vier Adressen, nicht ein Angebot.
 */

?>
<section class="abschnitt">
  <div class="bahn schmal">
    <h1>Diese Seite gibt es nicht.</h1>
    <p class="lede">Vielleicht wurde die Adresse geändert oder eine alte Seite ist umgezogen.</p>

    <ul class="hakenliste">
      <li><a href="/">Startseite</a></li>
      <li><a href="/leistungen">Leistungen</a></li>
      <li><a href="/preise">Preise</a></li>
      <li><a href="/briefing">Bedarf prüfen lassen</a></li>
    </ul>
  </div>
</section>
