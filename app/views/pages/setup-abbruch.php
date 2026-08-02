<?php

declare(strict_types=1);

?>
<h1>Die Einrichtung wurde abgebrochen</h1>
<p>
  Sie haben diese Seite über eine unverschlüsselte Verbindung aufgerufen. Bei der Einrichtung
  geben Sie Zugangsdaten ein. Die gehen nicht im Klartext über die Leitung.
</p>
<h2>So kommen Sie weiter</h2>
<ul>
  <li>Richten Sie HTTPS für diese Domain ein und rufen Sie die Adresse erneut auf.</li>
  <li>Auf dem Entwicklungsrechner: <code>APP_ENV=local</code> setzen und über <code>http://localhost</code> aufrufen.</li>
</ul>
<p class="leise">
  Weiterleitungs-Kopfzeilen wie <code>X-Forwarded-Proto</code> zählen hier nicht. Sie lassen sich
  frei setzen.
</p>
