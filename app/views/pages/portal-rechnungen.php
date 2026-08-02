<?php

declare(strict_types=1);

use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Rechnungsdienst;
use Sartu\Services\Zahlungsstatus;

/**
 * `/portal/rechnungen` — Portal-Lastenheft §8.5.
 *
 * **Der Knopf fuehrt zum Zahlungsdienst und kommt nicht zurueck.** Es gibt keine
 * Rueckkehradresse und keinen Parameter, aus dem sich ein Zahlungsstatus ableiten liesse
 * (§12, Testfall 14). Wer bezahlt hat, sieht es hier, sobald ein Mensch den Eingang
 * geprueft hat.
 *
 * @var list<array<string,mixed>> $rechnungen
 * @var array<string,mixed>|null $projekt
 */

?>
<h1>Ihre Rechnungen</h1>

<?php if ($rechnungen === []): ?>
<div class="karte">
  <p>Sobald eine Rechnung vorliegt, sehen Sie sie hier mit Betrag, Fälligkeit und
  Zahlungsweg.</p>
</div>
<?php else: ?>
<?php foreach ($rechnungen as $rechnung): ?>
<?php
$brutto = (int) $rechnung['gross_cents'];
$bezahlt = (int) $rechnung['paid_cents'];
$rest = Zahlungsstatus::restbetrag($bezahlt, $brutto);
$link = $rechnung['mollie_payment_url'] ?? null;
?>
<div class="karte">
  <h2><?= Html::e((string) $rechnung['number']) ?></h2>
  <ul class="pruefliste">
    <li><span>Betreff</span><span><?= Html::e(Rechnungsdienst::MEILENSTEINE[(string) $rechnung['milestone']] ?? '') ?></span></li>
    <li><span>Nettobetrag</span><span><?= Html::e(Format::euro((int) $rechnung['net_cents'])) ?></span></li>
<?php if ((int) $rechnung['vat_cents'] > 0): ?>
    <li><span>Umsatzsteuer</span><span><?= Html::e(Format::euro((int) $rechnung['vat_cents'])) ?></span></li>
<?php endif; ?>
    <li><span>Gesamtbetrag</span><span><?= Html::e(Format::euro($brutto)) ?></span></li>
    <li><span>Stand</span><span><?= Html::e(Zahlungsstatus::kundentext($rechnung)) ?></span></li>
  </ul>

<?php if ($rest > 0 && (string) $rechnung['status'] !== 'storniert'): ?>
<?php if (is_string($link) && $link !== ''): ?>
  <p class="knopfreihe">
    <a class="knopf" href="<?= Html::e($link) ?>" rel="noopener noreferrer" target="_blank">Jetzt bezahlen</a>
  </p>
  <p class="leise">Der Zahlungsweg öffnet sich in einem neuen Fenster. Nach der Zahlung
  prüfen wir den Eingang und tragen ihn hier ein — das dauert in der Regel einen Werktag.</p>
<?php else: ?>
  <p class="leise">Den Zahlungsweg stellen wir Ihnen in Kürze hier bereit.</p>
<?php endif; ?>
<?php endif; ?>
</div>
<?php endforeach; ?>
<?php endif; ?>

<?php if ($projekt !== null && $projekt['protection_started_on'] !== null): ?>
<p class="fussnote">Betrieb seit <?= Html::e(Format::datum((string) $projekt['protection_started_on'])) ?>
<?php if ($projekt['protection_min_term_until'] !== null): ?>
 · Mindestlaufzeit bis <?= Html::e(Format::datum((string) $projekt['protection_min_term_until'])) ?>
<?php endif; ?>
</p>
<?php endif; ?>
