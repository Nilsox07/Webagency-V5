<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Preise;
use Sartu\Services\Projektstatus;

/**
 * Ein Projekt mit seinen Angeboten — Portal-Lastenheft §4, §4c, §5.1a.
 *
 * Das Angebotsformular ist aus §4c vorbelegt. Die drei festen Texte stehen in den Feldern
 * und sind aenderbar, duerfen aber nicht leer bleiben — geprueft wird im `AngebotDienst`,
 * nicht hier.
 *
 * @var array<string,mixed> $projekt
 * @var list<array<string,mixed>> $angebote
 * @var array<string,mixed>|null $vorbelegung
 * @var list<string> $fehler
 * @var list<string> $hinweise
 */

$zustaende = [
    'entwurf'        => 'Entwurf — für den Kunden unsichtbar',
    'gesendet'       => 'Gesendet',
    'angenommen'     => 'Angenommen',
    'abgelaufen'     => 'Abgelaufen',
    'zurueckgezogen' => 'Zurückgezogen',
];

?>
<p class="vorzeile"><a href="/admin/projekte">Zurück zur Liste</a></p>
<h1><?= Html::e(Format::text((string) $projekt['title'])) ?></h1>

<?= Ansicht::teil('partials/meldungen', ['fehler' => $fehler, 'hinweise' => $hinweise]) ?>

<div class="karte">
  <h2>Projektstand</h2>
  <ul class="pruefliste">
    <li><span>Umfang</span><span><?= Html::e(Preise::name((string) $projekt['package'])) ?></span></li>
    <li><span>Zustand</span><span><?= Html::e(Projektstatus::kundentext((string) $projekt['status'])) ?></span></li>
    <li><span>Korrekturrunden</span><span><?= Html::e((string) $projekt['included_feedback_rounds']) ?></span></li>
    <li><span>Rundum-Schutz</span><span><?= Html::e($projekt['protection_level'] === null ? Format::LEER : 'Schutz ' . mb_strtoupper((string) $projekt['protection_level'])) ?></span></li>
  </ul>
</div>

<div class="karte">
  <h2>Angebote</h2>
<?php if ($angebote === []): ?>
  <p>Zu diesem Projekt gibt es noch kein Angebot.</p>
<?php else: ?>
  <ul class="liste">
<?php foreach ($angebote as $angebot): ?>
    <li>
      <span><?= Html::e((string) $angebot['number']) ?> · <?= Html::e(Format::euro((int) $angebot['one_time_net_cents'])) ?> einmalig</span>
      <span>
        <span class="marke" data-stand="<?= Html::e((string) $angebot['status']) ?>"><?= Html::e($zustaende[(string) $angebot['status']] ?? (string) $angebot['status']) ?></span>
<?php if ((string) $angebot['status'] === 'entwurf'): ?>
        <form method="post" action="/admin/angebote/<?= Html::e((string) $angebot['id']) ?>/senden">
          <?= Csrf::feld() ?>
          <button type="submit" class="knopf">Angebot senden</button>
        </form>
<?php endif; ?>
      </span>
    </li>
<?php endforeach; ?>
  </ul>
<?php endif; ?>
</div>

<div class="karte">
  <h2>Rechnungen</h2>
<?php if ($rechnungen === []): ?>
  <p>Zu diesem Projekt gibt es noch keine Rechnung.</p>
<?php else: ?>
  <ul class="liste">
<?php foreach ($rechnungen as $rechnung): ?>
    <li>
      <span><a href="/admin/rechnungen/<?= Html::e((string) $rechnung['id']) ?>"><?= Html::e((string) $rechnung['number']) ?></a>
        · <?= Html::e(\Sartu\Services\Rechnungsdienst::MEILENSTEINE[(string) $rechnung['milestone']] ?? '') ?></span>
      <span><?= Html::e(Format::euro((int) $rechnung['gross_cents'])) ?> · <?= Html::e((string) $rechnung['status']) ?></span>
    </li>
<?php endforeach; ?>
  </ul>
<?php endif; ?>

  <form method="post" action="/admin/projekte/<?= Html::e((string) $projekt['id']) ?>/rechnung">
    <?= Csrf::feld() ?>
    <div class="feldpaar">
      <div class="feld">
        <label for="feld-re-number">Rechnungsnummer</label>
        <input type="text" id="feld-re-number" name="number" value="" placeholder="RE-2026-001" required>
      </div>
      <div class="feld">
        <label for="feld-milestone">Betreff</label>
        <select id="feld-milestone" name="milestone">
<?php foreach (\Sartu\Services\Rechnungsdienst::MEILENSTEINE as $wert => $beschriftung): ?>
          <option value="<?= Html::e($wert) ?>"><?= Html::e($beschriftung) ?></option>
<?php endforeach; ?>
        </select>
      </div>
      <div class="feld">
        <label for="feld-net_cents">Nettobetrag in Cent</label>
        <input type="number" id="feld-net_cents" name="net_cents" value="" min="1" required>
      </div>
      <div class="feld">
        <label for="feld-due_date">Fällig am</label>
        <input type="date" id="feld-due_date" name="due_date" value="">
        <p class="feld__hinweis">Leer lassen: 10 Kalendertage ab heute.</p>
      </div>
    </div>
    <button type="submit" class="knopf knopf--ruhig">Rechnung als Entwurf anlegen</button>
  </form>
</div>

<?php if ($vorbelegung !== null): ?>
<div class="karte">
  <h2>Neues Angebot</h2>
  <p class="leise">Die festen Texte aus §4c sind vorbelegt. Sie sind änderbar, dürfen aber
  nicht leer bleiben.</p>

  <form method="post" action="/admin/projekte/<?= Html::e((string) $projekt['id']) ?>/angebot">
    <?= Csrf::feld() ?>
    <input type="hidden" name="package" value="<?= Html::e((string) $vorbelegung['package']) ?>">
    <input type="hidden" name="protection_level" value="<?= Html::e((string) $vorbelegung['protection_level']) ?>">
    <input type="hidden" name="included_feedback_rounds" value="<?= Html::e((string) $vorbelegung['included_feedback_rounds']) ?>">

    <div class="feldpaar">
      <div class="feld">
        <label for="feld-number">Angebotsnummer</label>
        <input type="text" id="feld-number" name="number" value="" placeholder="AN-2026-001" required>
      </div>
      <div class="feld">
        <label for="feld-valid_until">Gültig bis</label>
        <input type="date" id="feld-valid_until" name="valid_until"
          value="<?= Html::e((string) $vorbelegung['valid_until']) ?>" required>
        <p class="feld__hinweis">Vorbelegt mit 30 Kalendertagen. Änderbar.</p>
      </div>
    </div>

    <div class="feld">
      <label for="feld-summary">Zusammenfassung des Ziels</label>
      <textarea id="feld-summary" name="summary" required></textarea>
    </div>

    <div class="feld">
      <label for="feld-sitemap">Vorgesehene Seitenstruktur</label>
      <textarea id="feld-sitemap" name="sitemap" required></textarea>
    </div>

    <div class="feld">
      <label for="feld-inclusions">Was enthalten ist</label>
      <textarea id="feld-inclusions" name="inclusions" required><?= Html::e((string) $vorbelegung['inclusions']) ?></textarea>
    </div>

    <div class="feld">
      <label for="feld-exclusions">Was nicht enthalten ist</label>
      <textarea id="feld-exclusions" name="exclusions" required><?= Html::e((string) $vorbelegung['exclusions']) ?></textarea>
    </div>

    <div class="feldpaar">
      <div class="feld">
        <label for="feld-scope_pages">Umfangsgrenze: Seiten</label>
        <input type="number" id="feld-scope_pages" name="scope_pages" value="">
      </div>
      <div class="feld">
        <label for="feld-scope_words">Umfangsgrenze: Wörter</label>
        <input type="number" id="feld-scope_words" name="scope_words" value="">
      </div>
    </div>

    <div class="feldpaar">
      <div class="feld">
        <label for="feld-delivery_days_min">Lieferkorridor von (Werktage)</label>
        <input type="number" id="feld-delivery_days_min" name="delivery_days_min"
          value="<?= Html::e((string) ($vorbelegung['delivery_days_min'] ?? '')) ?>" required>
      </div>
      <div class="feld">
        <label for="feld-delivery_days_max">bis</label>
        <input type="number" id="feld-delivery_days_max" name="delivery_days_max"
          value="<?= Html::e((string) ($vorbelegung['delivery_days_max'] ?? '')) ?>" required>
      </div>
    </div>

    <div class="feld">
      <label for="feld-delivery_start_condition">Wann der Zeitraum beginnt</label>
      <textarea id="feld-delivery_start_condition" name="delivery_start_condition" required><?= Html::e((string) $vorbelegung['delivery_start_condition']) ?></textarea>
    </div>

    <div class="feldpaar">
      <div class="feld">
        <label for="feld-one_time_net_cents">Einmalpreis netto in Cent</label>
        <input type="number" id="feld-one_time_net_cents" name="one_time_net_cents"
          value="<?= Html::e((string) $vorbelegung['one_time_net_cents']) ?>" required>
      </div>
      <div class="feld">
        <label for="feld-protection_monthly_net_cents">Betrieb netto je Monat in Cent</label>
        <input type="number" id="feld-protection_monthly_net_cents" name="protection_monthly_net_cents"
          value="<?= Html::e((string) $vorbelegung['protection_monthly_net_cents']) ?>" required>
      </div>
      <div class="feld">
        <label for="feld-first_year_net_cents">Erstjahreswert netto in Cent</label>
        <input type="number" id="feld-first_year_net_cents" name="first_year_net_cents"
          value="<?= Html::e((string) $vorbelegung['first_year_net_cents']) ?>" required>
        <p class="feld__hinweis">Muss Einmalpreis + 12 × Betrieb ergeben.</p>
      </div>
    </div>

    <div class="feld">
      <label for="feld-payment_plan">Zahlungsplan</label>
      <select id="feld-payment_plan" name="payment_plan">
        <option value="50_50">50 % bei Auftrag, 50 % nach Abnahme</option>
        <option value="40_30_30">40 % / 30 % / 30 %</option>
        <option value="custom">Eigener Plan (nur Sonderprojekt)</option>
      </select>
    </div>

    <div class="feld">
      <label for="feld-payment_plan_custom">Eigener Zahlungsplan</label>
      <textarea id="feld-payment_plan_custom" name="payment_plan_custom"></textarea>
      <p class="feld__hinweis">Eine Rate je Zeile: Bezeichnung | Betrag netto | Fälligkeit.
      Die Summe muss dem Einmalpreis entsprechen.</p>
    </div>

    <div class="feld">
      <label for="feld-rights_text">Rechte und Export</label>
      <textarea id="feld-rights_text" name="rights_text" required><?= Html::e((string) $vorbelegung['rights_text']) ?></textarea>
    </div>

    <div class="feld">
      <label for="feld-domain_text">Domain und E-Mail</label>
      <textarea id="feld-domain_text" name="domain_text" required><?= Html::e((string) $vorbelegung['domain_text']) ?></textarea>
    </div>

    <fieldset class="frage">
      <legend>Schließen Besucher über die Seite einen Vertrag ab — Buchung, Bestellung oder Abonnement?</legend>
      <ul class="wahl">
        <li><input type="radio" id="feld-bfsg1-nein" name="bfsg_vertragsabschluss" value="nein" checked>
          <label for="feld-bfsg1-nein">Nein</label></li>
        <li><input type="radio" id="feld-bfsg1-ja" name="bfsg_vertragsabschluss" value="ja">
          <label for="feld-bfsg1-ja">Ja</label></li>
      </ul>
    </fieldset>

    <fieldset class="frage">
      <legend>Hat der Betrieb weniger als 10 Beschäftigte und höchstens 2 Mio. € Umsatz oder Bilanzsumme?</legend>
      <ul class="wahl">
        <li><input type="radio" id="feld-bfsg2-ja" name="bfsg_kleinstunternehmen" value="ja">
          <label for="feld-bfsg2-ja">Ja</label></li>
        <li><input type="radio" id="feld-bfsg2-nein" name="bfsg_kleinstunternehmen" value="nein">
          <label for="feld-bfsg2-nein">Nein</label></li>
        <li><input type="radio" id="feld-bfsg2-unbekannt" name="bfsg_kleinstunternehmen" value="unbekannt" checked>
          <label for="feld-bfsg2-unbekannt">Unbekannt</label></li>
      </ul>
      <p class="feld__hinweis">Es sind Angaben des Kunden, keine Feststellung von SARTU.</p>
    </fieldset>

    <button type="submit" class="knopf">Angebot als Entwurf speichern</button>
  </form>
</div>
<?php endif; ?>
