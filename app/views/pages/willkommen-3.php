<?php

declare(strict_types=1);

use Sartu\Helpers\Csrf;

/**
 * Willkommensstrecke, Bildschirm 3 von 3 — Portal-Lastenheft §7.
 *
 * > „**Der Hinweis zum passwortlosen Anmelden ist Pflicht und darf nicht gekuerzt werden.**
 * > Kunden erwarten ein Passwort; ohne Erklaerung entsteht der Eindruck, etwas sei kaputt
 * > oder unsicher."
 *
 * Der Knopf ist ein `POST`: Er setzt `welcome_seen_at`. Ein Link waere ein GET, der etwas
 * aendert.
 */

?>
<p class="vorzeile">Bildschirm 3 von 3</p>
<h1>Sie sehen immer genau einen nächsten Schritt.</h1>

<p>Oben in Ihrem Bereich steht, was gerade von Ihnen gebraucht wird. Mehr müssen Sie nicht im
Blick behalten — wir melden uns, wenn etwas ansteht.</p>

<p><strong>Anmelden ohne Passwort.</strong> Sie bekommen jedes Mal einen Link per E-Mail. Es
gibt kein Passwort, das verloren gehen kann.</p>

<p><strong>Wenn etwas unklar ist</strong>, schreiben Sie uns. Wir antworten schriftlich — meist
am selben oder nächsten Werktag.</p>

<div class="knopfreihe">
  <form method="post" action="/willkommen/fertig">
    <?= Csrf::feld() ?>
    <button type="submit" class="knopf">Meinen Bereich öffnen</button>
  </form>
  <a class="knopf knopf--ruhig" href="/willkommen/2">Zurück</a>
</div>
