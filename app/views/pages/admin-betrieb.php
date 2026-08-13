<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Html;

/** @var list<string> $fehler */
/** @var list<string> $hinweise */
/** @var array<string,mixed> $werte */
/** @var string|null $bild der Ablagename des Gründerbildes, `null` wenn keines liegt */

$wert = static fn (string $feld): string => (string) ($werte[$feld] ?? '');

?>
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

  <h2>Auftragslage</h2>
  <p>Beides steht leise unter dem Knopf auf der Startseite. Bleibt die Auftragslage auf
  „Nicht gesetzt", steht dort nichts — das ist der Regelfall, solange Sie nichts zusagen
  wollen.</p>
  <div class="feldpaar">
    <div class="feld">
      <label for="feld-auftragslage">Auftragslage</label>
      <select id="feld-auftragslage" name="auftragslage">
        <option value=""<?= $wert('auftragslage') === '' ? ' selected' : '' ?>>Nicht gesetzt — es wird nichts angezeigt</option>
<?php foreach (['offen' => 'Freie Kapazitäten', 'knapp' => 'Wenig frei', 'ausgebucht' => 'Ausgebucht — Warteliste'] as $schluessel => $beschriftung): ?>
        <option value="<?= Html::e($schluessel) ?>"<?= $wert('auftragslage') === $schluessel ? ' selected' : '' ?>><?= Html::e($beschriftung) ?></option>
<?php endforeach; ?>
      </select>
    </div>
    <?= Ansicht::teil('components/feld', [
        'name' => 'naechster_projektstart',
        'beschriftung' => 'Nächster möglicher Projektstart',
        'art' => 'date',
        'wert' => $wert('naechster_projektstart'),
        'hinweis' => 'Angezeigt wird nur der Monat. Liegt der Tag in der Vergangenheit, entfällt die Zeile von selbst.',
    ]) ?>
  </div>

  <h2>Wer dahintersteckt</h2>
  <p>Die Sektion auf der Startseite und der Abschnitt auf „Über uns" erscheinen erst, wenn
  Name, Text <strong>und</strong> Bild vorliegen. Fehlt eines davon, entfällt beides —
  ein halber Abschnitt an dieser Stelle schadet mehr als keiner.</p>
  <?= Ansicht::teil('components/feld', [
      'name' => 'gruender_name',
      'beschriftung' => 'Name der Person hinter SARTU',
      'wert' => $wert('gruender_name'),
      'hinweis' => 'Steht öffentlich auf der Startseite und wird vorgelesen. Voller Name oder Vorname und Rolle — beides ist möglich.',
  ]) ?>
  <div class="feld">
    <label for="feld-gruender_text">Warum es SARTU gibt</label>
    <textarea id="feld-gruender_text" name="gruender_text" rows="5"><?= Html::e($wert('gruender_text')) ?></textarea>
    <p class="feld__hinweis">Zwei bis drei Sätze in Alltagssprache: welche Beobachtung dazu
    geführt hat, dieses Angebot zu bauen. Kein Lebenslauf, keine Stationen, keine Zahlen.</p>
  </div>

  <div class="feld">
    <label for="feld-profil_adressen">Profilseiten</label>
    <textarea id="feld-profil_adressen" name="profil_adressen" rows="3"><?= Html::e($wert('profil_adressen')) ?></textarea>
    <p class="feld__hinweis">Eine Adresse je Zeile, jede beginnt mit https://. Sie stehen
    nicht auf der Seite, sondern nur in den strukturierten Daten — sie sagen Suchmaschinen,
    welche Profile zu SARTU gehören. Bleibt das Feld leer, wird nichts ausgeliefert.</p>
  </div>

  <?= Ansicht::teil('components/feld', ['name' => 'grund', 'beschriftung' => 'Grund der Änderung', 'pflicht' => true, 'hinweis' => 'Steht später im Protokoll. Ein Stichwort genügt.']) ?>

  <div class="knopfreihe">
    <button class="knopf" type="submit">Betreiberdaten speichern</button>
  </div>
</form>

<?php /* Eigenes Formular, weil ein Upload `multipart/form-data` braucht — sonst liefe bei
         jedem Speichern der Anschrift eine Dateiannahme mit. */ ?>
<div class="karte">
  <h2>Bild der Person hinter SARTU</h2>
<?php if ($bild === null): ?>
  <p>Noch nicht hinterlegt. Ohne Bild entfällt die Sektion „Wer dahintersteckt" vollständig.</p>
<?php else: ?>
  <p>Ein Bild liegt vor. So steht es auf der Seite:</p>
  <?php /* Kein `style`-Attribut: Die CSP setzt `style-src 'self'` ohne `unsafe-inline`,
           ein Attribut hier waere im Browser wirkungslos. */ ?>
  <p><img class="bildprobe" src="/bild/gruender" alt="Das hinterlegte Bild der Person hinter SARTU." width="220" height="275"></p>
<?php endif; ?>

  <form method="post" action="/admin/einstellungen/gruenderbild" enctype="multipart/form-data">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-bild">Bilddatei</label>
      <input type="file" id="feld-bild" name="bild" accept="image/jpeg,image/png,image/webp" required>
      <p class="feld__hinweis">JPG, PNG oder WebP, höchstens 20 MB. Ein echtes Foto —
      kein Bestandsbild und kein Platzhalter, der wie ein Foto wirkt.</p>
    </div>
    <button type="submit" class="knopf">Bild hinterlegen</button>
  </form>

<?php if ($bild !== null): ?>
  <form method="post" action="/admin/einstellungen/gruenderbild-entfernen">
    <?= Csrf::feld() ?>
    <button type="submit" class="knopf knopf--ruhig">Bild entfernen</button>
  </form>
<?php endif; ?>
</div>
