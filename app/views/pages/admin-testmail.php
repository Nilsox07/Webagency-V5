<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;

/** @var list<string> $fehler */
/** @var list<string> $hinweise */

?>
<h1>Testnachricht senden</h1>
<p>Prüft den Mailversand mit den hinterlegten Zugangsdaten.</p>
<p>Nehmen Sie eine Adresse außerhalb Ihrer eigenen Domain. Sonst sagt der Versand wenig.</p>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler, 'hinweise' => $hinweise]) ?>

<form method="post" action="/admin/testmail" class="karte">
  <?= Csrf::feld() ?>
  <?= Ansicht::teil('components/feld', ['name' => 'an', 'beschriftung' => 'Empfänger', 'art' => 'email', 'pflicht' => true]) ?>
  <div class="knopfreihe">
    <button class="knopf" type="submit">Testnachricht senden</button>
  </div>
</form>
