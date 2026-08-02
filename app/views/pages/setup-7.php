<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Html;

/** @var list<string> $fehler */
/** @var array<string,string> $werte */
/** @var string $geheimnis */
/** @var string $lesbar */
/** @var string $adresse */

?>
<h1>Ihr Zugang: Passwort und zweiter Faktor</h1>
<p>Sie vergeben das Passwort selbst. Es gibt kein voreingestelltes und kein Standardkonto.</p>
<p>Der zweite Faktor ist Pflicht. Er lässt sich auch später nicht abschalten.</p>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler]) ?>

<form method="post" action="/admin/setup/admin" class="karte">
  <?= Csrf::feld() ?>
  <div class="feldpaar">
    <?= Ansicht::teil('components/feld', ['name' => 'vorname', 'beschriftung' => 'Vorname', 'wert' => $werte['vorname'] ?? '', 'pflicht' => true, 'autovervollstaendigung' => 'given-name']) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'nachname', 'beschriftung' => 'Nachname', 'wert' => $werte['nachname'] ?? '', 'pflicht' => true, 'autovervollstaendigung' => 'family-name']) ?>
  </div>
  <?= Ansicht::teil('components/feld', ['name' => 'email', 'beschriftung' => 'E-Mail-Adresse', 'art' => 'email', 'wert' => $werte['email'] ?? '', 'pflicht' => true, 'autovervollstaendigung' => 'username']) ?>
  <div class="feldpaar">
    <?= Ansicht::teil('components/feld', ['name' => 'passwort', 'beschriftung' => 'Passwort', 'art' => 'password', 'pflicht' => true, 'autovervollstaendigung' => 'new-password', 'hinweis' => 'Mindestens zwölf Zeichen.']) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'passwort_wiederholung', 'beschriftung' => 'Passwort wiederholen', 'art' => 'password', 'pflicht' => true, 'autovervollstaendigung' => 'new-password']) ?>
  </div>

  <h2>Schlüssel in die Authenticator-App eintragen</h2>
  <p>Tippen Sie den Schlüssel in Ihre App ein. Sie zeigt Ihnen danach einen sechsstelligen Code.</p>
  <p class="schluesselwert"><?= Html::e($lesbar) ?></p>
  <p class="feld__hinweis">Konto: <?= Html::e($konto) ?></p>

  <p>Nimmt Ihre App eine Adresse entgegen, geht es auch damit — kopieren statt tippen:</p>
  <p class="schluesselwert"><?= Html::e($adresse) ?></p>

  <?= Ansicht::teil('components/feld', ['name' => 'code', 'beschriftung' => 'Sechsstelliger Code aus der App', 'pflicht' => true, 'autovervollstaendigung' => 'one-time-code', 'hinweis' => 'Der Code wechselt alle 30 Sekunden.']) ?>

  <div class="knopfreihe">
    <button class="knopf" type="submit">Konto anlegen und Code prüfen</button>
  </div>
</form>
