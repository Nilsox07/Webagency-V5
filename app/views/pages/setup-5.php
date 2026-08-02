<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;

/** @var list<string> $fehler */
/** @var array<string,string> $werte */
/** @var bool $gesendet */

?>
<h1>Eine Testnachricht muss ankommen</h1>
<p>Der gesamte Zugang läuft später über Mail. Kommt sie nicht an, funktioniert nichts.</p>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler]) ?>

<?php if ($gesendet): ?>
<div class="meldung meldung--hinweis">
  <p><strong>Die Nachricht ist raus.</strong></p>
  <p>Sehen Sie in Ihrem Posteingang nach, auch im Spam-Ordner. Bestätigen Sie erst danach.</p>
</div>
<form method="post" action="/admin/setup/mail-bestaetigen">
  <?= Csrf::feld() ?>
  <div class="knopfreihe">
    <button class="knopf" type="submit">Empfang bestätigen</button>
    <a class="knopf knopf--ruhig" href="/admin/setup">Zugang ändern</a>
  </div>
</form>
<?php else: ?>
<form method="post" action="/admin/setup/mail" class="karte">
  <?= Csrf::feld() ?>
  <div class="feldpaar">
    <?= Ansicht::teil('components/feld', ['name' => 'smtp_host', 'beschriftung' => 'SMTP-Server', 'wert' => $werte['smtp_host'] ?? '', 'pflicht' => true]) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'smtp_port', 'beschriftung' => 'Port', 'wert' => $werte['smtp_port'] ?? '587']) ?>
  </div>
  <div class="feldpaar">
    <?= Ansicht::teil('components/feld', ['name' => 'smtp_user', 'beschriftung' => 'Benutzer', 'wert' => $werte['smtp_user'] ?? '', 'autovervollstaendigung' => 'off']) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'smtp_pass', 'beschriftung' => 'Passwort', 'art' => 'password', 'autovervollstaendigung' => 'new-password']) ?>
  </div>
  <?= Ansicht::teil('components/feld', ['name' => 'mail_from', 'beschriftung' => 'Absenderadresse', 'art' => 'email', 'wert' => $werte['mail_from'] ?? '', 'pflicht' => true]) ?>
  <?= Ansicht::teil('components/feld', ['name' => 'an', 'beschriftung' => 'Testnachricht senden an', 'art' => 'email', 'wert' => $werte['an'] ?? '', 'pflicht' => true, 'hinweis' => 'Nehmen Sie eine Adresse außerhalb Ihrer eigenen Domain.']) ?>
  <div class="knopfreihe">
    <button class="knopf" type="submit">Testnachricht senden</button>
  </div>
</form>
<?php endif; ?>
