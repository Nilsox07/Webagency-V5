<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/** @var list<string> $hindernisse */
/** @var array<string,mixed>|null $betreiber */

?>
<h1>Übersicht</h1>

<?php if ($hindernisse === []): ?>
<div class="meldung meldung--hinweis">
  <p><strong>Der Start ist frei.</strong> Betreiberdaten und Rechtstexte sind vollständig.</p>
</div>
<?php else: ?>
<div class="meldung">
  <p><strong>Vor der Veröffentlichung fehlt noch etwas.</strong></p>
  <ul>
<?php foreach ($hindernisse as $hindernis): ?>
    <li><?= Html::e($hindernis) ?></li>
<?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<div class="karte">
  <h2>Was Sie hier erledigen</h2>
  <ul class="liste">
    <li><a href="/admin/einstellungen/betrieb">Betreiberdaten pflegen</a><span class="leise">Impressum, Rechnungen, Anmeldeseite</span></li>
    <li><a href="/admin/rechtstexte">Rechtstexte verwalten</a><span class="leise">Entwurf, Prüfung, Freigabe</span></li>
    <li><a href="/admin/testmail">Testnachricht senden</a><span class="leise">Prüft den Mailversand</span></li>
  </ul>
</div>
