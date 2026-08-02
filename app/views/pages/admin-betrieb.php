<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;

/** @var list<string> $fehler */
/** @var list<string> $hinweise */
/** @var array<string,mixed> $werte */

$wert = static fn (string $feld): string => (string) ($werte[$feld] ?? '');

?>
<h1>Betreiberdaten</h1>
<p>Diese Angaben ziehen Impressum, Fußbereich, Rechnungen und die Anmeldeseite.</p>
<p>Jede Änderung wird protokolliert. Der Grund ist deshalb ein Pflichtfeld.</p>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler, 'hinweise' => $hinweise]) ?>

<form method="post" action="/admin/einstellungen/betrieb" class="karte">
  <?= Csrf::feld() ?>
  <div class="feldpaar">
    <?= Ansicht::teil('components/feld', ['name' => 'firmenname', 'beschriftung' => 'Firmenname', 'wert' => $wert('firmenname'), 'pflicht' => true]) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'rechtsform', 'beschriftung' => 'Rechtsform', 'wert' => $wert('rechtsform')]) ?>
  </div>
  <?= Ansicht::teil('components/feld', ['name' => 'strasse', 'beschriftung' => 'Straße und Hausnummer', 'wert' => $wert('strasse'), 'pflicht' => true, 'hinweis' => 'Ein Postfach genügt nicht. Das Impressum verlangt eine ladungsfähige Anschrift.']) ?>
  <div class="feldpaar">
    <?= Ansicht::teil('components/feld', ['name' => 'plz', 'beschriftung' => 'Postleitzahl', 'wert' => $wert('plz'), 'pflicht' => true]) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'ort', 'beschriftung' => 'Ort', 'wert' => $wert('ort'), 'pflicht' => true]) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'land', 'beschriftung' => 'Land', 'wert' => $wert('land'), 'pflicht' => true]) ?>
  </div>
  <div class="feldpaar">
    <?= Ansicht::teil('components/feld', ['name' => 'email', 'beschriftung' => 'E-Mail-Adresse', 'art' => 'email', 'wert' => $wert('email'), 'pflicht' => true]) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'telefon', 'beschriftung' => 'Telefonnummer', 'wert' => $wert('telefon')]) ?>
  </div>
  <?= Ansicht::teil('components/feld', ['name' => 'inhaltlich_verantwortlich', 'beschriftung' => 'Inhaltlich verantwortlich', 'wert' => $wert('inhaltlich_verantwortlich'), 'pflicht' => true]) ?>

  <h2>Steuer und Register</h2>
  <div class="feldpaar">
    <?= Ansicht::teil('components/feld', ['name' => 'ust_id', 'beschriftung' => 'Umsatzsteuer-Identifikationsnummer', 'wert' => $wert('ust_id')]) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'steuernummer', 'beschriftung' => 'Steuernummer', 'wert' => $wert('steuernummer')]) ?>
  </div>
  <div class="feldpaar">
    <?= Ansicht::teil('components/feld', ['name' => 'registergericht', 'beschriftung' => 'Registergericht', 'wert' => $wert('registergericht')]) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'registernummer', 'beschriftung' => 'Registernummer', 'wert' => $wert('registernummer')]) ?>
  </div>
  <div class="feld">
    <label for="feld-kleinunternehmer">Kleinunternehmer nach § 19 UStG</label>
    <select id="feld-kleinunternehmer" name="kleinunternehmer">
      <option value="0"<?= $wert('kleinunternehmer') === '1' ? '' : ' selected' ?>>Nein, ich weise Umsatzsteuer aus</option>
      <option value="1"<?= $wert('kleinunternehmer') === '1' ? ' selected' : '' ?>>Ja, ich weise keine Umsatzsteuer aus</option>
    </select>
    <p class="feld__hinweis">Steht das auf Ja, erscheint nirgends „zzgl. USt." — weder auf der Website noch auf Rechnungen.</p>
  </div>

  <h2>Bankverbindung</h2>
  <div class="feldpaar">
    <?= Ansicht::teil('components/feld', ['name' => 'bank_iban', 'beschriftung' => 'IBAN', 'wert' => $wert('bank_iban')]) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'bank_bic', 'beschriftung' => 'BIC', 'wert' => $wert('bank_bic')]) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'bank_institut', 'beschriftung' => 'Bank', 'wert' => $wert('bank_institut')]) ?>
  </div>

  <?= Ansicht::teil('components/feld', ['name' => 'grund', 'beschriftung' => 'Grund der Änderung', 'pflicht' => true, 'hinweis' => 'Steht später im Protokoll. Ein Stichwort genügt.']) ?>

  <div class="knopfreihe">
    <button class="knopf" type="submit">Betreiberdaten speichern</button>
  </div>
</form>
