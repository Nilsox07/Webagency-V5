<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;

/** @var list<string> $fehler */
/** @var array<string,string> $werte */

?>
<h1>Jetzt die Datenbank</h1>
<p>Wir testen die Verbindung sofort. Gespeichert wird erst, wenn sie steht.</p>
<p>Die Datenbank muss leer sein. In fremden Bestand richtet sich nichts ein.</p>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler]) ?>

<form method="post" action="/admin/setup/datenbank" class="karte">
  <?= Csrf::feld() ?>
  <div class="feldpaar">
    <?= Ansicht::teil('components/feld', ['name' => 'db_host', 'beschriftung' => 'Server', 'wert' => $werte['db_host'] ?? 'localhost', 'pflicht' => true]) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'db_port', 'beschriftung' => 'Port', 'wert' => $werte['db_port'] ?? '3306']) ?>
  </div>
  <?= Ansicht::teil('components/feld', ['name' => 'db_name', 'beschriftung' => 'Name der Datenbank', 'wert' => $werte['db_name'] ?? '', 'pflicht' => true]) ?>
  <div class="feldpaar">
    <?= Ansicht::teil('components/feld', ['name' => 'db_user', 'beschriftung' => 'Benutzer', 'wert' => $werte['db_user'] ?? '', 'pflicht' => true, 'autovervollstaendigung' => 'off']) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'db_pass', 'beschriftung' => 'Passwort', 'art' => 'password', 'autovervollstaendigung' => 'new-password']) ?>
  </div>
  <div class="knopfreihe">
    <button class="knopf" type="submit">Verbindung testen und speichern</button>
  </div>
</form>
