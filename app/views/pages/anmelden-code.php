<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;

/** @var list<string> $fehler */

?>
<h1>Der Code aus Ihrer App</h1>
<p>Er hat sechs Stellen und wechselt alle 30 Sekunden.</p>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler]) ?>

<form method="post" action="/admin/anmelden/code" class="karte">
  <?= Csrf::feld() ?>
  <?= Ansicht::teil('components/feld', ['name' => 'code', 'beschriftung' => 'Sechsstelliger Code', 'pflicht' => true, 'autovervollstaendigung' => 'one-time-code']) ?>
  <div class="knopfreihe">
    <button class="knopf" type="submit">Anmeldung abschließen</button>
    <a class="knopf knopf--ruhig" href="/admin/anmelden">Von vorn beginnen</a>
  </div>
</form>
