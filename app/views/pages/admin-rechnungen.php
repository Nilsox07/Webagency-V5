<?php

declare(strict_types=1);

use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Rechnungsdienst;
use Sartu\Services\Zahlungsstatus;

/**
 * Die Rechnungsliste im internen Bereich.
 *
 * @var list<array<string,mixed>> $rechnungen
 * @var array<string,string> $projekte
 * @var string|null $zustand
 */

$zustaende = [
    'entwurf'           => 'Entwurf',
    'gesendet'          => 'Gesendet',
    'teilweise_bezahlt' => 'Teilweise bezahlt',
    'bezahlt'           => 'Bezahlt',
    'ueberfaellig'      => 'Überfällig',
    'storniert'         => 'Storniert',
];

?>
<h1>Rechnungen</h1>
<p>Zahlungseingänge werden von Hand eingetragen, nachdem sie im Zahlungsdienst geprüft
wurden. Jede Eintragung braucht einen Grundlagentext und wird protokolliert.</p>

<form method="get" action="/admin/rechnungen" class="filterzeile">
  <div class="feld">
    <label for="feld-zustand">Zustand</label>
    <select id="feld-zustand" name="zustand">
      <option value="">Alle</option>
<?php foreach ($zustaende as $wert => $beschriftung): ?>
      <option value="<?= Html::e($wert) ?>" <?= $zustand === $wert ? 'selected' : '' ?>><?= Html::e($beschriftung) ?></option>
<?php endforeach; ?>
    </select>
  </div>
  <button type="submit" class="knopf knopf--ruhig">Liste filtern</button>
</form>

<?php if ($rechnungen === []): ?>
<div class="karte">
  <p>Sobald Sie zu einem Projekt eine Rechnung anlegen, steht sie hier.</p>
</div>
<?php else: ?>
<div class="karte">
  <div class="tabellenrahmen">
    <table class="tabelle">
      <thead>
        <tr>
          <th scope="col">Nummer</th>
          <th scope="col">Projekt</th>
          <th scope="col">Betreff</th>
          <th scope="col">Brutto</th>
          <th scope="col">Bezahlt</th>
          <th scope="col">Fällig</th>
          <th scope="col">Zustand</th>
        </tr>
      </thead>
      <tbody>
<?php foreach ($rechnungen as $rechnung): ?>
        <tr>
          <td><a href="/admin/rechnungen/<?= Html::e((string) $rechnung['id']) ?>"><?= Html::e((string) $rechnung['number']) ?></a></td>
          <td><?= Html::e($projekte[(string) $rechnung['project_id']] ?? Format::LEER) ?></td>
          <td><?= Html::e(Rechnungsdienst::MEILENSTEINE[(string) $rechnung['milestone']] ?? '') ?></td>
          <td><?= Html::e(Format::euro((int) $rechnung['gross_cents'])) ?></td>
          <td><?= Html::e(Format::euro((int) $rechnung['paid_cents'])) ?></td>
          <td><?= Html::e(Format::datum($rechnung['due_date'] === null ? null : (string) $rechnung['due_date'])) ?></td>
          <td><?= Html::e($zustaende[(string) $rechnung['status']] ?? (string) $rechnung['status']) ?></td>
        </tr>
<?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
