<?php

declare(strict_types=1);

use Sartu\Helpers\Csrf;
use Sartu\Helpers\Html;

/**
 * Die Kontaktdaten — Website-Lastenheft §9.4, erst **nach** dem Ergebnis.
 *
 * §9.4 verbietet hier zwei Dinge: **kein** Newsletter-Häkchen, **keine** Pflicht-Telefonnummer.
 *
 * **Eine Abweichung von der Feldliste, begründet:** §9.4 nennt ein Feld „Vor- und Nachname".
 * Die Tabelle `leads` hat `first_name` und `last_name` getrennt und beide `NOT NULL`
 * (Portal-Lastenheft §4). Ein Feld auf zwei Spalten aufzuteilen hiesse raten, wo der
 * Nachname anfängt — bei „von der Heide" rät man falsch. Es stehen deshalb zwei Felder da,
 * mit der einen vorgegebenen Fehlermeldung darüber.
 *
 * **Der Honigtopf** (§9.5b) heisst `hp_website`, ist `aria-hidden` und aus der Tabreihenfolge
 * genommen. Er wird über das Stylesheet verborgen, nicht über `type="hidden"` — ein
 * verstecktes Feld füllt kein Automat aus, und genau darauf zielt die Falle.
 *
 * @var array<string,mixed>  $werte
 * @var array<string,string> $fehler
 * @var string|null          $meldung
 * @var string|null          $kontaktweg
 * @var array<string,string> $quellen
 */

$wert = static fn (string $feld): string => is_string($werte[$feld] ?? null) ? (string) $werte[$feld] : '';
$ersterFehler = array_key_first($fehler);

?>
<p class="vorzeile">Letzter Schritt</p>
<h1>Wohin sollen wir das geprüfte Angebot schicken?</h1>

<?php if ($meldung !== null): ?>
<div class="meldung" role="alert">
  <p><?= Html::e($meldung) ?></p>
<?php if ($kontaktweg !== null): ?>
  <p>Oder schreiben Sie uns an <a href="mailto:<?= Html::e($kontaktweg) ?>"><?= Html::e($kontaktweg) ?></a>.</p>
<?php endif; ?>
</div>
<?php endif; ?>

<form method="post" action="/briefing/absenden">
  <?= Csrf::feld() ?>

  <fieldset class="frage<?= isset($fehler['name']) ? ' frage--fehler' : '' ?>">
    <legend>Vor- und Nachname</legend>
<?php if (isset($fehler['name'])): ?>
    <p class="frage__fehler" id="feld-name-fehler"><?= Html::e($fehler['name']) ?></p>
<?php endif; ?>
    <label for="feld-first_name">Vorname</label>
    <input type="text" id="feld-first_name" name="first_name" value="<?= Html::e($wert('first_name')) ?>"
      autocomplete="given-name" required
      <?= isset($fehler['name']) ? 'aria-describedby="feld-name-fehler"' : '' ?>
      <?= $ersterFehler === 'name' ? 'autofocus' : '' ?>>
    <label for="feld-last_name">Nachname</label>
    <input type="text" id="feld-last_name" name="last_name" value="<?= Html::e($wert('last_name')) ?>"
      autocomplete="family-name" required>
  </fieldset>

  <div class="frage<?= isset($fehler['company']) ? ' frage--fehler' : '' ?>">
    <label for="feld-company">Unternehmen</label>
<?php if (isset($fehler['company'])): ?>
    <p class="frage__fehler" id="feld-company-fehler"><?= Html::e($fehler['company']) ?></p>
<?php endif; ?>
    <input type="text" id="feld-company" name="company" value="<?= Html::e($wert('company')) ?>"
      autocomplete="organization" required
      <?= isset($fehler['company']) ? 'aria-describedby="feld-company-fehler"' : '' ?>
      <?= $ersterFehler === 'company' ? 'autofocus' : '' ?>>
  </div>

  <div class="frage<?= isset($fehler['email']) ? ' frage--fehler' : '' ?>">
    <label for="feld-email">Geschäftliche E-Mail-Adresse</label>
<?php if (isset($fehler['email'])): ?>
    <p class="frage__fehler" id="feld-email-fehler"><?= Html::e($fehler['email']) ?></p>
<?php endif; ?>
    <input type="email" id="feld-email" name="email" value="<?= Html::e($wert('email')) ?>"
      autocomplete="email" inputmode="email" required
      <?= isset($fehler['email']) ? 'aria-describedby="feld-email-fehler"' : '' ?>
      <?= $ersterFehler === 'email' ? 'autofocus' : '' ?>>
  </div>

  <div class="frage">
    <label for="feld-phone">Telefon <span class="frage__optional">(optional)</span></label>
    <input type="tel" id="feld-phone" name="phone" value="<?= Html::e($wert('phone')) ?>"
      autocomplete="tel" inputmode="tel">
  </div>

  <fieldset class="frage<?= isset($fehler['preferred_contact']) ? ' frage--fehler' : '' ?>">
    <legend>Wie sollen wir Sie erreichen?</legend>
<?php if (isset($fehler['preferred_contact'])): ?>
    <p class="frage__fehler" id="feld-preferred_contact-fehler"><?= Html::e($fehler['preferred_contact']) ?></p>
<?php endif; ?>
    <ul class="wahl">
      <li>
        <input type="radio" id="feld-kontakt-email" name="preferred_contact" value="email"
          <?= $wert('preferred_contact') === 'email' ? 'checked' : '' ?>
          <?= isset($fehler['preferred_contact']) ? 'aria-describedby="feld-preferred_contact-fehler"' : '' ?>
          <?= $ersterFehler === 'preferred_contact' ? 'autofocus' : '' ?>>
        <label for="feld-kontakt-email">E-Mail</label>
      </li>
      <li>
        <input type="radio" id="feld-kontakt-portal" name="preferred_contact" value="portal"
          <?= $wert('preferred_contact') === 'portal' ? 'checked' : '' ?>>
        <label for="feld-kontakt-portal">Kundenbereich</label>
      </li>
    </ul>
  </fieldset>

  <fieldset class="frage">
    <legend>Wie sind Sie auf uns aufmerksam geworden? <span class="frage__optional">(optional)</span></legend>
    <ul class="wahl">
<?php foreach ($quellen as $schluessel => $beschriftung): ?>
      <li>
        <input type="radio" id="feld-quelle-<?= Html::e($schluessel) ?>" name="self_reported_source"
          value="<?= Html::e($schluessel) ?>" <?= $wert('self_reported_source') === $schluessel ? 'checked' : '' ?>>
        <label for="feld-quelle-<?= Html::e($schluessel) ?>"><?= Html::e($beschriftung) ?></label>
      </li>
<?php endforeach; ?>
    </ul>
    <label for="feld-quelle-text">Ergänzung <span class="frage__optional">(optional)</span></label>
    <input type="text" id="feld-quelle-text" name="self_reported_source_text"
      value="<?= Html::e($wert('self_reported_source_text')) ?>">
  </fieldset>

  <div class="frage<?= isset($fehler['b2b_confirmed']) ? ' frage--fehler' : '' ?>">
<?php if (isset($fehler['b2b_confirmed'])): ?>
    <p class="frage__fehler" id="feld-b2b-fehler"><?= Html::e($fehler['b2b_confirmed']) ?></p>
<?php endif; ?>
    <p class="haken">
      <input type="checkbox" id="feld-b2b" name="b2b_confirmed" value="1"
        <?= $wert('b2b_confirmed') === '1' ? 'checked' : '' ?>
        <?= isset($fehler['b2b_confirmed']) ? 'aria-describedby="feld-b2b-fehler"' : '' ?>
        <?= $ersterFehler === 'b2b_confirmed' ? 'autofocus' : '' ?>>
      <label for="feld-b2b">Ich handle für mein Unternehmen bzw. in Ausübung meiner beruflichen oder
        gewerblichen Tätigkeit.</label>
    </p>
  </div>

  <div class="frage<?= isset($fehler['privacy_confirmed']) ? ' frage--fehler' : '' ?>">
<?php if (isset($fehler['privacy_confirmed'])): ?>
    <p class="frage__fehler" id="feld-datenschutz-fehler"><?= Html::e($fehler['privacy_confirmed']) ?></p>
<?php endif; ?>
    <p class="haken">
      <input type="checkbox" id="feld-datenschutz" name="privacy_confirmed" value="1"
        <?= $wert('privacy_confirmed') === '1' ? 'checked' : '' ?>
        <?= isset($fehler['privacy_confirmed']) ? 'aria-describedby="feld-datenschutz-fehler"' : '' ?>
        <?= $ersterFehler === 'privacy_confirmed' ? 'autofocus' : '' ?>>
      <label for="feld-datenschutz">Ich habe die <a href="/datenschutz">Datenschutzhinweise</a> gelesen.</label>
    </p>
  </div>

  <?php /* Honigtopf — §9.5b. Sichtbar nur für Automaten. */ ?>
  <div class="honigtopf" aria-hidden="true">
    <label for="feld-hp_website">Website</label>
    <input type="text" id="feld-hp_website" name="hp_website" value="" tabindex="-1" autocomplete="off">
  </div>

  <div class="knopfreihe">
    <button type="submit" class="knopf">Anfrage abschicken</button>
    <a class="knopf knopf--ruhig" href="/briefing/ergebnis">Zurück zur Empfehlung</a>
  </div>
</form>

<p class="fussnote">Wir prüfen Ihre Anfrage persönlich. Verbindlich ist erst das von SARTU
geprüfte Angebot.</p>
