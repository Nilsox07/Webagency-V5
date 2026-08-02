<?php

declare(strict_types=1);

use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Domainstand;

/**
 * `/portal/domain` — Portal-Lastenheft §8.7.
 *
 * Der Stand wird vom Admin gepflegt. Der Kunde liest ihn — er traegt hier nichts ein, weil
 * die Registrar-Anbindung Stufe C ist und ein Eingabefeld eine Automatik andeuten wuerde,
 * die es nicht gibt (§0.3b).
 *
 * @var array<string,mixed>|null $projekt
 * @var array<string,mixed>|null $stand
 */

?>
<h1>Domain</h1>

<?php if ($stand === null): ?>
<div class="karte">
  <p>Sobald wir Ihre Domain prüfen, sehen Sie hier den Stand. Sie müssen nichts tun — wir
  melden uns, wenn wir etwas von Ihnen brauchen.</p>
</div>
<?php else: ?>
<div class="karte">
  <ul class="pruefliste">
    <li><span>Stand</span><span><?= Html::e(Domainstand::kundentext((string) $stand['state'])) ?></span></li>
    <li><span>Wunschname</span><span><?= Html::e(Format::text($stand['desired_name'] === null ? null : (string) $stand['desired_name'])) ?></span></li>
    <li><span>Bestätigter Name</span><span><?= Html::e(Format::text($stand['confirmed_name'] === null ? null : (string) $stand['confirmed_name'])) ?></span></li>
    <li><span>Auf Ihren Namen registriert</span><span><?= (int) $stand['owner_confirmed'] === 1 ? 'Ja' : 'Noch nicht' ?></span></li>
  </ul>
<?php if ($stand['email_note'] !== null && trim((string) $stand['email_note']) !== ''): ?>
  <h2>Ihre E-Mail-Adressen</h2>
  <p><?= nl2br(Html::e((string) $stand['email_note'])) ?></p>
<?php endif; ?>
</div>
<?php endif; ?>

<?php if ($projekt !== null && $projekt['live_url'] !== null): ?>
<div class="karte karte--betont">
  <p>Ihre Website ist erreichbar unter
  <a href="<?= Html::e((string) $projekt['live_url']) ?>" target="_blank" rel="noopener noreferrer"><?= Html::e((string) $projekt['live_url']) ?></a>.</p>
</div>
<?php endif; ?>
