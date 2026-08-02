<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Rechnungsdienst;
use Sartu\Services\Zahlungsstatus;

/**
 * Eine Rechnung im internen Bereich — Portal-Lastenheft §12.
 *
 * Das Formular fuer den Zahlungseingang traegt die Pflichtbestaetigung aus §12 im Wortlaut
 * und ein Pflichtfeld fuer den Grundlagentext. Beides ist keine Zierde: Der Text wird als
 * `reason` protokolliert und ist spaeter der Nachweis, worauf sich die Buchung stuetzt.
 *
 * @var array<string,mixed> $rechnung
 * @var array<string,mixed>|null $projekt
 * @var list<string> $fehler
 * @var list<string> $hinweise
 */

$id = (string) $rechnung['id'];
$brutto = (int) $rechnung['gross_cents'];
$bezahlt = (int) $rechnung['paid_cents'];

?>
<p class="vorzeile"><a href="/admin/rechnungen">Zurück zur Liste</a></p>
<h1>Rechnung <?= Html::e((string) $rechnung['number']) ?></h1>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler, 'hinweise' => $hinweise]) ?>

<div class="karte">
  <h2>Stand</h2>
  <ul class="pruefliste">
    <li><span>Projekt</span><span><?= $projekt === null ? Html::e(Format::LEER)
        : '<a href="/admin/projekte/' . Html::e((string) $projekt['id']) . '">' . Html::e((string) $projekt['title']) . '</a>' ?></span></li>
    <li><span>Betreff</span><span><?= Html::e(Rechnungsdienst::MEILENSTEINE[(string) $rechnung['milestone']] ?? '') ?></span></li>
    <li><span>Netto</span><span><?= Html::e(Format::euro((int) $rechnung['net_cents'])) ?></span></li>
    <li><span>Umsatzsteuer</span><span><?= Html::e(Format::euro((int) $rechnung['vat_cents'])) ?></span></li>
    <li><span>Brutto</span><span><?= Html::e(Format::euro($brutto)) ?></span></li>
    <li><span>Bezahlt</span><span><?= Html::e(Format::euro($bezahlt)) ?></span></li>
    <li><span>Offen</span><span><?= Html::e(Format::euro(Zahlungsstatus::restbetrag($bezahlt, $brutto))) ?></span></li>
<?php if (Zahlungsstatus::ueberzahlung($bezahlt, $brutto) > 0): ?>
    <li><span>Überzahlung</span><span><?= Html::e(Format::euro(Zahlungsstatus::ueberzahlung($bezahlt, $brutto))) ?></span></li>
<?php endif; ?>
    <li><span>Fällig am</span><span><?= Html::e(Format::datum($rechnung['due_date'] === null ? null : (string) $rechnung['due_date'])) ?></span></li>
    <li><span>Zustand</span><span><?= Html::e((string) $rechnung['status']) ?></span></li>
    <li><span>Erste Erinnerung</span><span><?= Html::e(Format::datumZeit($rechnung['reminder_sent_at'] === null ? null : (string) $rechnung['reminder_sent_at'])) ?></span></li>
    <li><span>Zweite Erinnerung</span><span><?= Html::e(Format::datumZeit($rechnung['reminder2_sent_at'] === null ? null : (string) $rechnung['reminder2_sent_at'])) ?></span></li>
  </ul>
</div>

<?php if ((string) $rechnung['status'] === 'entwurf'): ?>
<div class="karte">
  <h2>Senden</h2>
  <p>Der Kunde sieht die Rechnung erst nach dem Senden.</p>
  <form method="post" action="/admin/rechnungen/<?= Html::e($id) ?>/senden">
    <?= Csrf::feld() ?>
    <button type="submit" class="knopf">Rechnung senden</button>
  </form>
</div>
<?php endif; ?>

<div class="karte">
  <h2>Zahlungslink</h2>
  <p>Erzeugen Sie den Link im Zahlungsdienst und tragen Sie ihn hier ein.</p>
  <form method="post" action="/admin/rechnungen/<?= Html::e($id) ?>/zahlungslink">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-mollie">Adresse des Zahlungslinks</label>
      <input type="url" id="feld-mollie" name="mollie_payment_url"
        value="<?= Html::e($rechnung['mollie_payment_url'] === null ? '' : (string) $rechnung['mollie_payment_url']) ?>">
    </div>
    <button type="submit" class="knopf knopf--ruhig">Link speichern</button>
  </form>
</div>

<?php if ((string) $rechnung['status'] !== 'entwurf' && (string) $rechnung['status'] !== 'storniert'): ?>
<div class="karte">
  <h2>Zahlungseingang eintragen</h2>
  <p>Bestätigen Sie, dass der Zahlungseingang im Zahlungsdienst geprüft wurde. Diese Aktion
  wird protokolliert.</p>
  <form method="post" action="/admin/rechnungen/<?= Html::e($id) ?>/zahlung">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-paid_cents">Eingegangener Betrag in Cent</label>
      <input type="number" id="feld-paid_cents" name="paid_cents" value="<?= Html::e((string) $bezahlt) ?>" min="0" required>
      <p class="feld__hinweis">Bei <?= Html::e(Format::euro($brutto)) ?> vollständig: <?= Html::e((string) $brutto) ?>.</p>
    </div>
    <div class="feld">
      <label for="feld-grundlage">Grundlage der Prüfung</label>
      <input type="text" id="feld-grundlage" name="grundlage" value="" required minlength="3"
        placeholder="Mollie-Zahlung tr_xxx vom 04.08.2026">
      <p class="feld__hinweis">Pflichtfeld. Er steht später im Protokoll als Nachweis.</p>
    </div>
    <button type="submit" class="knopf">Zahlungseingang eintragen</button>
  </form>
</div>

<div class="karte">
  <h2>Stornieren</h2>
  <form method="post" action="/admin/rechnungen/<?= Html::e($id) ?>/stornieren">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-storno">Grundlage</label>
      <input type="text" id="feld-storno" name="grundlage" value="" required minlength="3">
    </div>
    <button type="submit" class="knopf knopf--ruhig">Rechnung stornieren</button>
  </form>
</div>
<?php endif; ?>
