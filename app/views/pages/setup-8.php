<?php

declare(strict_types=1);

use Sartu\Helpers\Csrf;
use Sartu\Helpers\Html;

/** @var string $cronBefehl */

?>
<h1>Ein Schritt fehlt noch auf dem Server</h1>
<p>Tragen Sie diesen Befehl bei Ihrem Anbieter als tägliche Aufgabe ein.</p>
<p class="schluesselwert"><?= Html::e($cronBefehl) ?></p>
<p class="leise">Er räumt abgelaufene Anmeldungen ab. Ab Stufe A1 kommen weitere Läufe dazu.</p>

<div class="karte">
  <h2>Danach ist die Einrichtung gesperrt</h2>
  <p>Diese Seite liefert dann eine Fehlermeldung. Über das Netz lässt sich das nicht zurücknehmen.</p>
  <p>Ein Zurücksetzen verlangt Dateizugriff auf dem Server.</p>
</div>

<form method="post" action="/admin/setup/abschluss">
  <?= Csrf::feld() ?>
  <div class="knopfreihe">
    <button class="knopf" type="submit">Einrichtung abschließen</button>
  </div>
</form>
