<?php

declare(strict_types=1);

use Sartu\Data\Admin\AdminAnfragen;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Empfehlung;
use Sartu\Services\Preise;

/**
 * Die Anfrageliste — Portal-Lastenheft §4b.5.
 *
 * Spalten in der vorgegebenen Reihenfolge: Eingangsdatum · Firma · Name · empfohlener Umfang ·
 * Ampelkennzeichen · Status · Löschdatum. Sortierung nach Eingang, neueste zuerst.
 *
 * **Kein Rot für „rot".** Es gibt eine Akzentfarbe (`SARTU_DESIGNSYSTEM.md`), und ein
 * Ampelkennzeichen ist kein Grund, eine zweite einzuführen. Unterschieden wird über Fläche
 * und Kante — und über das Wort selbst, das ohnehin dasteht.
 *
 * @var list<array<string,mixed>> $anfragen
 * @var string|null $zustand
 * @var string|null $quelle
 * @var list<string> $quellen
 */

?>
<h1>Anfragen</h1>
<p>Eingegangene Bedarfsschecks, neueste zuerst. Ein Zugang entsteht ausschließlich durch einen
bewussten Klick, nie automatisch.</p>

<form method="get" action="/admin/anfragen" class="filterzeile">
  <div class="feld">
    <label for="feld-zustand">Zustand</label>
    <select id="feld-zustand" name="zustand">
      <option value="">Alle</option>
<?php foreach (AdminAnfragen::ZUSTANDS_BESCHRIFTUNGEN as $wert => $beschriftung): ?>
      <option value="<?= Html::e($wert) ?>" <?= $zustand === $wert ? 'selected' : '' ?>><?= Html::e($beschriftung) ?></option>
<?php endforeach; ?>
    </select>
  </div>
<?php if ($quellen !== []): ?>
  <div class="feld">
    <label for="feld-quelle">Kampagne</label>
    <select id="feld-quelle" name="quelle">
      <option value="">Alle</option>
<?php foreach ($quellen as $eine): ?>
      <option value="<?= Html::e($eine) ?>" <?= $quelle === $eine ? 'selected' : '' ?>><?= Html::e($eine) ?></option>
<?php endforeach; ?>
    </select>
  </div>
<?php endif; ?>
  <button type="submit" class="knopf knopf--ruhig">Liste filtern</button>
</form>

<?php if ($anfragen === []): ?>
<div class="karte">
  <p><?= $zustand === null && $quelle === null
      ? 'Sobald jemand den Bedarfsscheck abschickt, steht die Anfrage hier.'
      : 'Zu dieser Auswahl gibt es keine Anfrage. Setzen Sie den Filter zurück, um alle zu sehen.' ?></p>
</div>
<?php else: ?>
<div class="karte">
  <div class="tabellenrahmen">
    <table class="tabelle">
      <thead>
        <tr>
          <th scope="col">Eingang</th>
          <th scope="col">Firma</th>
          <th scope="col">Name</th>
          <th scope="col">Empfohlener Umfang</th>
          <th scope="col">Kennzeichen</th>
          <th scope="col">Zustand</th>
          <th scope="col">Löschdatum</th>
        </tr>
      </thead>
      <tbody>
<?php foreach ($anfragen as $anfrage): ?>
        <tr>
          <td><a href="/admin/anfragen/<?= Html::e((string) $anfrage['id']) ?>"><?= Html::e(Format::datumZeit((string) $anfrage['submitted_at'])) ?></a></td>
          <td><?= Html::e(Format::text((string) $anfrage['company'])) ?></td>
          <td><?= Html::e(trim((string) $anfrage['first_name'] . ' ' . (string) $anfrage['last_name'])) ?></td>
          <td><?= Html::e($anfrage['recommended_package'] === null
              ? Format::LEER
              : Preise::name((string) $anfrage['recommended_package'])) ?></td>
          <td><span class="marke" data-stand="<?= Html::e((string) $anfrage['flag']) ?>"><?= Html::e(Empfehlung::ampelName((string) $anfrage['flag'])) ?></span></td>
          <td><?= Html::e(AdminAnfragen::ZUSTANDS_BESCHRIFTUNGEN[(string) $anfrage['status']] ?? (string) $anfrage['status']) ?></td>
          <td><?= Html::e(Format::datum((string) $anfrage['delete_after'])) ?></td>
        </tr>
<?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
