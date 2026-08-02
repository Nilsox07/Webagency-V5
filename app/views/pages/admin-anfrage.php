<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Data\Admin\AdminAnfragen;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Empfehlung;
use Sartu\Services\Preise;

/**
 * Eine Anfrage im Detail — Portal-Lastenheft §4b.5.
 *
 * > „Detailansicht: **alle** Antworten in Klartext als Frage → Antwort, nicht als Rohdaten."
 *
 * Die Umsetzung dieser Zeile steht in `Bedarfsscheck::klartext()`. Hier wird sie nur
 * ausgegeben — eine Ansicht rechnet nichts (§1.3).
 *
 * **`Endgültig löschen` steht unten und braucht einen Grund.** Die Aktion ist die einzige
 * Ausnahme von §3 Regel 13 und nicht rückgängig zu machen. Ein Knopf neben „Notiz speichern"
 * wäre eine Einladung zum Verklicken.
 *
 * @var array<string,mixed> $anfrage
 * @var list<array{frage:string,antwort:string}> $antworten
 * @var list<string> $fehler
 * @var list<string> $hinweise
 */

$id = (string) $anfrage['id'];

$herkunft = [
    'Landeseite'      => $anfrage['landing_page'] ?? null,
    'Verweisender Host' => $anfrage['referrer_host'] ?? null,
    'Kampagnenquelle' => $anfrage['utm_source'] ?? null,
    'Kanal'           => $anfrage['utm_medium'] ?? null,
    'Kampagne'        => $anfrage['utm_campaign'] ?? null,
    'Suchbegriff'     => $anfrage['utm_term'] ?? null,
    'Anzeigeninhalt'  => $anfrage['utm_content'] ?? null,
    'Klickkennung'    => $anfrage['click_id'] ?? null,
    'Selbst genannt'  => $anfrage['self_reported_source'] ?? null,
];

?>
<p class="vorzeile"><a href="/admin/anfragen">Zurück zur Liste</a></p>
<h1>Anfrage von <?= Html::e(Format::text((string) $anfrage['company'])) ?></h1>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler, 'hinweise' => $hinweise]) ?>

<div class="karte">
  <h2>Kontakt und Einordnung</h2>
  <ul class="pruefliste">
    <li><span>Eingang</span><span><?= Html::e(Format::datumZeit((string) $anfrage['submitted_at'])) ?></span></li>
    <li><span>Name</span><span><?= Html::e(trim((string) $anfrage['first_name'] . ' ' . (string) $anfrage['last_name'])) ?></span></li>
    <li><span>E-Mail</span><span><?= Html::e(Format::text((string) $anfrage['email'])) ?></span></li>
    <li><span>Telefon</span><span><?= Html::e(Format::text($anfrage['phone'] === null ? null : (string) $anfrage['phone'])) ?></span></li>
    <li><span>Bevorzugter Kontakt</span><span><?= Html::e((string) $anfrage['preferred_contact'] === 'portal' ? 'Kundenbereich' : 'E-Mail') ?></span></li>
    <li><span>Empfohlener Umfang</span><span><?= Html::e($anfrage['recommended_package'] === null
        ? Format::LEER : Preise::name((string) $anfrage['recommended_package'])) ?></span></li>
    <li><span>Kennzeichen</span><span><?= Html::e(Empfehlung::ampelName((string) $anfrage['flag'])) ?></span></li>
    <li><span>Zustand</span><span><?= Html::e(AdminAnfragen::ZUSTANDS_BESCHRIFTUNGEN[(string) $anfrage['status']] ?? (string) $anfrage['status']) ?></span></li>
    <li><span>Löschdatum</span><span><?= Html::e(Format::datum((string) $anfrage['delete_after'])) ?></span></li>
  </ul>
</div>

<div class="karte">
  <h2>Die Antworten aus dem Bedarfsscheck</h2>
<?php if ($antworten === []): ?>
  <p>Zu dieser Anfrage sind keine Antworten gespeichert.</p>
<?php else: ?>
  <ul class="pruefliste">
<?php foreach ($antworten as $zeile): ?>
    <li><span><?= Html::e($zeile['frage']) ?></span><span><?= Html::e($zeile['antwort']) ?></span></li>
<?php endforeach; ?>
  </ul>
<?php endif; ?>
</div>

<div class="karte">
  <h2>Herkunft</h2>
  <p class="leise">Erfasst beim ersten Seitenaufruf. Nur Pfad und Hostname, nie die vollständige
  Adresse.</p>
  <ul class="pruefliste">
<?php foreach ($herkunft as $beschriftung => $wert): ?>
    <li><span><?= Html::e((string) $beschriftung) ?></span><span><?= Html::e(Format::text($wert === null ? null : (string) $wert)) ?></span></li>
<?php endforeach; ?>
  </ul>
</div>

<div class="karte">
  <h2>Zustand ändern</h2>
  <p>Eine Ablehnung braucht eine Notiz und verkürzt die Löschfrist auf sechs Monate.</p>
  <form method="post" action="/admin/anfragen/<?= Html::e($id) ?>/zustand">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-zustand">Neuer Zustand</label>
      <select id="feld-zustand" name="zustand">
<?php foreach (AdminAnfragen::ZUSTANDS_BESCHRIFTUNGEN as $wert => $beschriftung): ?>
        <option value="<?= Html::e($wert) ?>" <?= (string) $anfrage['status'] === $wert ? 'selected' : '' ?>><?= Html::e($beschriftung) ?></option>
<?php endforeach; ?>
      </select>
    </div>
    <div class="feld">
      <label for="feld-notiz-zustand">Notiz</label>
      <input type="text" id="feld-notiz-zustand" name="notiz" value="">
      <p class="feld__hinweis">Bei „Abgelehnt" ist die Notiz Pflicht.</p>
    </div>
    <button type="submit" class="knopf">Zustand speichern</button>
  </form>
</div>

<div class="karte">
  <h2>Notiz</h2>
  <form method="post" action="/admin/anfragen/<?= Html::e($id) ?>/notiz">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-notiz">Interne Notiz</label>
      <textarea id="feld-notiz" name="notiz"><?= Html::e($anfrage['admin_note'] === null ? '' : (string) $anfrage['admin_note']) ?></textarea>
    </div>
    <button type="submit" class="knopf">Notiz speichern</button>
  </form>
</div>

<div class="karte">
  <h2>Auskunft und Löschung</h2>
  <p>Der Export enthält alles, was zu dieser Anfrage gespeichert ist.</p>
  <p class="knopfreihe">
    <a class="knopf knopf--ruhig" href="/admin/anfragen/<?= Html::e($id) ?>/export">Datensatz exportieren</a>
  </p>
  <form method="post" action="/admin/anfragen/<?= Html::e($id) ?>/loeschen">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-grund">Grund der Löschung</label>
      <input type="text" id="feld-grund" name="grund" value="" required>
      <p class="feld__hinweis">Der Datensatz ist danach weg. Protokolliert wird der Vorgang, nicht der Inhalt.</p>
    </div>
    <button type="submit" class="knopf knopf--ruhig">Endgültig löschen</button>
  </form>
</div>
