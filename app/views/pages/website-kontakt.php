<?php

declare(strict_types=1);

use Sartu\Helpers\Csrf;
use Sartu\Helpers\Html;
use Sartu\Services\Auftragslage;
use Sartu\Services\Firmenseitentexte as T;
use Sartu\Services\Kontaktanfrage;
use Sartu\Services\Websitetexte;

/**
 * `/kontakt` — Website-Lastenheft §11.
 *
 * **Zwei Karten, dann „Wo wir arbeiten", dann das Formular.** Die Reihenfolge ist
 * vorgegeben; §11 begründet sie beim Ortsabschnitt ausdrücklich.
 *
 * **Kein Dateiupload, keine Pflicht-Telefonnummer** (§11). Beides fehlt hier.
 *
 * **Sieben Felder, eine Bestätigung** — genau die Liste aus §11. Die B2B-Bestätigung stand
 * hier bis zum 02.08.2026 zusätzlich; sie war nur nötig, weil die Rückfrage in `leads`
 * abgelegt wurde und `chk_leads_bestaetigungen` beide Häkchen verlangt. §4b.6 verbietet die
 * Ablage — damit entfällt die Prüfbedingung und mit ihr das Häkchen.
 *
 * **Der Honigtopf** heißt wie beim Bedarfsscheck `hp_website` und wird über das Stylesheet
 * verborgen, nicht über `type="hidden"` — ein verstecktes Feld füllt kein Automat aus, und
 * genau darauf zielt die Falle.
 *
 * @var array<string,mixed> $werte
 * @var array<string,string> $fehler
 * @var string|null $meldung
 * @var string $zeitstempel
 * @var string $einreichung
 * @var bool $formularOffen
 * @var string|null $ausweichweg
 * @var array<string,string>|null $auftragslage
 * @var string $preishinweis
 */

$wert = static fn (string $feld): string => is_string($werte[$feld] ?? null) ? (string) $werte[$feld] : '';
$ersterFehler = array_key_first($fehler);

?>
<section class="aufmacher">
  <div class="bahn">
    <h1><?= Html::e(T::KONTAKT_H1) ?></h1>

    <div class="zweispalten">
      <article class="karte karte--betont">
        <h2>Websitebedarf prüfen</h2>
        <p>Wenige Fragen zu Ihrem Betrieb, danach eine vorläufige Empfehlung mit Preis. Etwa
        drei Minuten, unverbindlich.</p>
        <p><a class="knopf" href="/briefing"><?= Html::e(Auftragslage::knopf($auftragslage['knopf'] ?? null)) ?></a></p>
      </article>

      <article class="karte">
        <h2>Rückfrage stellen</h2>
        <p>Sie haben eine Frage zu Angebot, Domain oder Ablauf. Wir antworten schriftlich, in
        der Regel innerhalb eines Werktags.</p>
        <p><a class="textlink" href="#formular">Zum Formular</a></p>
      </article>
    </div>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn schmal">
    <h2><?= Html::e(T::WO_H2) ?></h2>
    <p class="lede"><?= Html::e(T::WO_TEXT) ?></p>
    <p><?= Html::e(T::WO_ZUSATZ) ?></p>
  </div>
</section>

<section class="abschnitt" id="formular">
  <div class="bahn schmal">
    <h2>Rückfrage stellen</h2>

<?php if (!$formularOffen): ?>
    <?php /* §0.3b: kein Formular, das jede Eingabe verliert. Begruendung im Kopf von
             `Kontaktanfrage::empfaengerVorhanden()`. */ ?>
    <p>Das Formular steht gerade nicht bereit.
<?php if ($ausweichweg !== null): ?>
    Schreiben Sie uns bitte an <a href="mailto:<?= Html::e($ausweichweg) ?>"><?= Html::e($ausweichweg) ?></a>.
<?php else: ?>
    Bitte nutzen Sie so lange den Bedarfsscheck.
<?php endif; ?>
    </p>
<?php else: ?>

<?php if ($meldung !== null): ?>
    <div class="meldung" role="alert">
      <p><?= Html::e($meldung) ?></p>
<?php if ($ausweichweg !== null): ?>
      <p>Oder schreiben Sie uns an <a href="mailto:<?= Html::e($ausweichweg) ?>"><?= Html::e($ausweichweg) ?></a>.</p>
<?php endif; ?>
    </div>
<?php endif; ?>

    <form method="post" action="/kontakt">
      <?= Csrf::feld() ?>
      <input type="hidden" name="form_started_at" value="<?= Html::e($zeitstempel) ?>">
      <input type="hidden" name="submission_id" value="<?= Html::e($einreichung) ?>">

      <div class="frage<?= isset($fehler['name']) ? ' frage--fehler' : '' ?>">
        <label for="feld-name">Ihr Name</label>
<?php if (isset($fehler['name'])): ?>
        <p class="frage__fehler" id="feld-name-fehler"><?= Html::e($fehler['name']) ?></p>
<?php endif; ?>
        <input type="text" id="feld-name" name="name" value="<?= Html::e($wert('name')) ?>"
          autocomplete="name" required
          <?= isset($fehler['name']) ? 'aria-describedby="feld-name-fehler"' : '' ?>
          <?= $ersterFehler === 'name' ? 'autofocus' : '' ?>>
      </div>

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

      <fieldset class="frage<?= isset($fehler['anliegen']) ? ' frage--fehler' : '' ?>">
        <legend>Worum geht es?</legend>
<?php if (isset($fehler['anliegen'])): ?>
        <p class="frage__fehler" id="feld-anliegen-fehler"><?= Html::e($fehler['anliegen']) ?></p>
<?php endif; ?>
        <ul class="wahl">
<?php foreach (Kontaktanfrage::ANLIEGEN as $schluessel => $beschriftung): ?>
          <li>
            <input type="radio" id="feld-anliegen-<?= Html::e($schluessel) ?>" name="anliegen"
              value="<?= Html::e($schluessel) ?>" <?= $wert('anliegen') === $schluessel ? 'checked' : '' ?>
              <?= isset($fehler['anliegen']) ? 'aria-describedby="feld-anliegen-fehler"' : '' ?>>
            <label for="feld-anliegen-<?= Html::e($schluessel) ?>"><?= Html::e($beschriftung) ?></label>
          </li>
<?php endforeach; ?>
        </ul>
      </fieldset>

      <div class="frage<?= isset($fehler['nachricht']) ? ' frage--fehler' : '' ?>">
        <label for="feld-nachricht">Ihre Nachricht</label>
<?php if (isset($fehler['nachricht'])): ?>
        <p class="frage__fehler" id="feld-nachricht-fehler"><?= Html::e($fehler['nachricht']) ?></p>
<?php endif; ?>
        <textarea id="feld-nachricht" name="nachricht" required
          <?= isset($fehler['nachricht']) ? 'aria-describedby="feld-nachricht-fehler"' : '' ?>
          <?= $ersterFehler === 'nachricht' ? 'autofocus' : '' ?>><?= Html::e($wert('nachricht')) ?></textarea>
      </div>

      <div class="frage<?= isset($fehler['privacy_confirmed']) ? ' frage--fehler' : '' ?>">
<?php if (isset($fehler['privacy_confirmed'])): ?>
        <p class="frage__fehler" id="feld-datenschutz-fehler"><?= Html::e($fehler['privacy_confirmed']) ?></p>
<?php endif; ?>
        <p class="haken">
          <input type="checkbox" id="feld-datenschutz" name="privacy_confirmed" value="1"
            <?= $wert('privacy_confirmed') === '1' ? 'checked' : '' ?>
            <?= isset($fehler['privacy_confirmed']) ? 'aria-describedby="feld-datenschutz-fehler"' : '' ?>>
          <label for="feld-datenschutz">Ich habe die <a href="/datenschutz">Datenschutzhinweise</a> gelesen.</label>
        </p>
      </div>

      <?php /* Honigtopf — §9.5b. Sichtbar nur für Automaten. */ ?>
      <div class="honigtopf" aria-hidden="true">
        <label for="feld-hp_website">Website</label>
        <input type="text" id="feld-hp_website" name="hp_website" value="" tabindex="-1" autocomplete="off">
      </div>

      <button type="submit" class="knopf">Nachricht senden</button>
    </form>
<?php endif; ?>

    <p class="fussnote"><?= Html::e(Websitetexte::ABSCHLUSSHINWEIS) ?> <?= Html::e($preishinweis) ?></p>
  </div>
</section>
