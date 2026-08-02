<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Html;

/** @var list<string> $fehler */
/** @var string|null $telefon */
/** @var string|null $email */

?>
<h1>Anmeldung</h1>
<p>Sie brauchen Ihr Passwort und den Code aus Ihrer Authenticator-App.</p>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler]) ?>

<form method="post" action="/admin/anmelden" class="karte">
  <?= Csrf::feld() ?>
  <?= Ansicht::teil('components/feld', ['name' => 'email', 'beschriftung' => 'E-Mail-Adresse', 'art' => 'email', 'pflicht' => true, 'autovervollstaendigung' => 'username']) ?>
  <?= Ansicht::teil('components/feld', ['name' => 'passwort', 'beschriftung' => 'Passwort', 'art' => 'password', 'pflicht' => true, 'autovervollstaendigung' => 'current-password']) ?>
  <div class="knopfreihe">
    <button class="knopf" type="submit">Weiter zum Code</button>
  </div>
</form>

<?php if ($telefon !== null): ?>
<p class="leise">Kommen Sie nicht weiter? Rufen Sie uns an: <?= Html::e($telefon) ?></p>
<?php elseif ($email !== null): ?>
<p class="leise">Kommen Sie nicht weiter? Schreiben Sie an <?= Html::e($email) ?></p>
<?php endif; ?>
