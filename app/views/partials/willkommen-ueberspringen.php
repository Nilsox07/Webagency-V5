<?php

declare(strict_types=1);

use Sartu\Helpers\Csrf;

/**
 * §7: „Ueberspringbar, jederzeit erneut aufrufbar." Ein Textlink, kein zweiter Hauptknopf —
 * er soll erreichbar sein, nicht werben.
 *
 * Als Formular, weil er `welcome_seen_at` setzt. Ohne JavaScript ist das der einzige
 * ehrliche Weg: Ein Link, der etwas aendert, ist ein GET, der etwas aendert.
 */

?>
<form method="post" action="/willkommen/fertig" class="ueberspringen">
  <?= Csrf::feld() ?>
  <button type="submit" class="textknopf">Überspringen</button>
</form>
