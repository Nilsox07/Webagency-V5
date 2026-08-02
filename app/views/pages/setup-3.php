<?php

declare(strict_types=1);

use Sartu\Helpers\Csrf;

?>
<h1>Zwei Schlüssel werden erzeugt</h1>
<p>Sie entstehen auf diesem Server und werden in die Datei <code>.env</code> geschrieben.</p>

<div class="karte">
  <h2>Wofür sie da sind</h2>
  <ul>
    <li><code>SESSION_SECRET</code> signiert die Anmeldungen.</li>
    <li><code>ENC_KEY</code> verschlüsselt den Schlüssel Ihrer Authenticator-App.</li>
  </ul>
  <p>
    Sichern Sie die Datei <code>.env</code> zusammen mit der Datenbank. Ohne
    <code>ENC_KEY</code> kommen Sie nach einer Wiederherstellung nicht mehr an Ihr Konto.
  </p>
</div>

<form method="post" action="/admin/setup/schluessel">
  <?= Csrf::feld() ?>
  <div class="knopfreihe">
    <button class="knopf" type="submit">Schlüssel erzeugen</button>
  </div>
</form>
