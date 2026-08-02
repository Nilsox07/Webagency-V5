<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;

/** @var list<string> $fehler */
/** @var array<string,string> $werte */

?>
<h1>Ihre Angaben für Impressum und Rechnungen</h1>
<p>Diese Werte stehen nirgends im Quelltext. Sie ändern sie später in Ihrem Bereich.</p>
<p>Vorläufige Angaben sind hier erlaubt. Vor der Veröffentlichung prüfen wir sie erneut.</p>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler]) ?>

<form method="post" action="/admin/setup/betrieb" class="karte">
  <?= Csrf::feld() ?>
  <div class="feldpaar">
    <?= Ansicht::teil('components/feld', ['name' => 'firmenname', 'beschriftung' => 'Firmenname', 'wert' => $werte['firmenname'] ?? '', 'pflicht' => true]) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'rechtsform', 'beschriftung' => 'Rechtsform', 'wert' => $werte['rechtsform'] ?? '']) ?>
  </div>
  <?= Ansicht::teil('components/feld', ['name' => 'strasse', 'beschriftung' => 'Straße und Hausnummer', 'wert' => $werte['strasse'] ?? '', 'pflicht' => true, 'hinweis' => 'Ein Postfach genügt nicht. Das Impressum verlangt eine ladungsfähige Anschrift.']) ?>
  <div class="feldpaar">
    <?= Ansicht::teil('components/feld', ['name' => 'plz', 'beschriftung' => 'Postleitzahl', 'wert' => $werte['plz'] ?? '', 'pflicht' => true]) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'ort', 'beschriftung' => 'Ort', 'wert' => $werte['ort'] ?? '', 'pflicht' => true]) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'land', 'beschriftung' => 'Land', 'wert' => $werte['land'] ?? 'DE', 'pflicht' => true, 'hinweis' => 'Zwei Buchstaben, zum Beispiel DE.']) ?>
  </div>
  <div class="feldpaar">
    <?= Ansicht::teil('components/feld', ['name' => 'email', 'beschriftung' => 'E-Mail-Adresse', 'art' => 'email', 'wert' => $werte['email'] ?? '', 'pflicht' => true]) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'telefon', 'beschriftung' => 'Telefonnummer', 'wert' => $werte['telefon'] ?? '', 'hinweis' => 'Steht auf der Anmeldeseite, wenn sie hinterlegt ist.']) ?>
  </div>
  <?= Ansicht::teil('components/feld', ['name' => 'inhaltlich_verantwortlich', 'beschriftung' => 'Inhaltlich verantwortlich', 'wert' => $werte['inhaltlich_verantwortlich'] ?? '', 'pflicht' => true]) ?>

  <h2>Eine Steuerangabe genügt</h2>
  <p>Tragen Sie die Umsatzsteuer-Identifikationsnummer ein oder die Steuernummer.</p>
  <div class="feldpaar">
    <?= Ansicht::teil('components/feld', ['name' => 'ust_id', 'beschriftung' => 'Umsatzsteuer-Identifikationsnummer', 'wert' => $werte['ust_id'] ?? '']) ?>
    <?= Ansicht::teil('components/feld', ['name' => 'steuernummer', 'beschriftung' => 'Steuernummer', 'wert' => $werte['steuernummer'] ?? '']) ?>
  </div>

  <div class="feld">
    <label for="feld-kleinunternehmer">Kleinunternehmer nach § 19 UStG</label>
    <select id="feld-kleinunternehmer" name="kleinunternehmer">
      <option value="0"<?= ($werte['kleinunternehmer'] ?? '0') === '0' ? ' selected' : '' ?>>Nein, ich weise Umsatzsteuer aus</option>
      <option value="1"<?= ($werte['kleinunternehmer'] ?? '0') === '1' ? ' selected' : '' ?>>Ja, ich weise keine Umsatzsteuer aus</option>
    </select>
    <p class="feld__hinweis">Steht das auf Ja, erscheint nirgends „zzgl. USt." — weder auf der Website noch auf Rechnungen.</p>
  </div>

  <div class="knopfreihe">
    <button class="knopf" type="submit">Betreiberdaten speichern</button>
  </div>
</form>
