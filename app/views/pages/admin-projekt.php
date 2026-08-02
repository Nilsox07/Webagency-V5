<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Data\Customer\KundenOeffnungszeiten;
use Sartu\Services\Domainstand;
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
 * @var list<array<string,mixed>> $runden
 * @var list<array<string,mixed>> $freigaben
 * @var array<string,mixed>|null $domainstand
 * @var list<array<string,mixed>> $zeiten
 * @var list<array<string,mixed>> $zeitausnahmen
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

<?php
$stand      = (string) $projekt['status'];
$rundentext = ['offen' => 'offen', 'eingereicht' => 'eingereicht', 'bearbeitet' => 'eingearbeitet'];
?>
<div class="karte">
  <h2>Vorschau und Korrekturrunden</h2>
  <ul class="pruefliste">
    <li><span>Vorschau</span><span><?= Html::e(Format::text($projekt['preview_url'] === null ? null : (string) $projekt['preview_url'])) ?></span></li>
    <li><span>Website</span><span><?= Html::e(Format::text($projekt['live_url'] === null ? null : (string) $projekt['live_url'])) ?></span></li>
  </ul>

<?php if ($runden === []): ?>
  <p>Zu diesem Projekt ist noch keine Korrekturrunde geöffnet.</p>
<?php else: ?>
  <ul class="liste">
<?php foreach ($runden as $runde): ?>
    <li>
      <span>Runde <?= Html::e((string) $runde['number']) ?>
        · <?= Html::e($rundentext[(string) $runde['status']] ?? (string) $runde['status']) ?>
        · <?= (int) $runde['included'] === 1 ? 'im Festpreis enthalten' : 'zusätzliche Runde' ?></span>
      <span>
<?php if ((string) $runde['status'] === 'eingereicht'): ?>
        <form method="post" action="/admin/projekte/<?= Html::e((string) $projekt['id']) ?>/runde">
          <?= Csrf::feld() ?>
          <input type="hidden" name="runde" value="<?= Html::e((string) $runde['id']) ?>">
          <button type="submit" class="knopf knopf--ruhig">Als eingearbeitet vermerken</button>
        </form>
<?php endif; ?>
      </span>
    </li>
<?php foreach ($runde['rueckmeldungen'] as $eintrag): ?>
    <li class="liste__unterzeile">
      <span><?= Html::e(Format::text($eintrag['page_hint'] === null ? null : (string) $eintrag['page_hint'])) ?></span>
      <span><?= Html::e((string) $eintrag['body']) ?></span>
    </li>
<?php endforeach; ?>
<?php endforeach; ?>
  </ul>
<?php endif; ?>

<?php if ($stand === Projektstatus::VORSCHAU || $stand === Projektstatus::KORREKTUR): ?>
  <form method="post" action="/admin/projekte/<?= Html::e((string) $projekt['id']) ?>/zusatzrunde">
    <?= Csrf::feld() ?>
    <p class="feld__hinweis">Eine zusätzliche Runde ist im Festpreis nicht enthalten. Der Kunde
    wird darauf hingewiesen; abgerechnet wird nichts von allein.</p>
    <button type="submit" class="knopf knopf--ruhig">Zusätzliche Runde öffnen</button>
  </form>
<?php endif; ?>

<?php if ($stand === Projektstatus::PRODUKTION || $stand === Projektstatus::KORREKTUR): ?>
  <form method="post" action="/admin/projekte/<?= Html::e((string) $projekt['id']) ?>/vorschau">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-preview_url">Adresse der Vorschau</label>
      <input type="url" id="feld-preview_url" name="preview_url" value="" placeholder="https://" required>
      <p class="feld__hinweis">Mit dem Bereitstellen öffnet sich die nächste Korrekturrunde.
      Ist sie nicht mehr im Festpreis enthalten, sieht der Kunde das vorher — gesperrt wird nichts.</p>
    </div>
    <button type="submit" class="knopf">Vorschau bereitstellen</button>
  </form>
<?php endif; ?>

<?php if ($stand === Projektstatus::VORSCHAU): ?>
  <form method="post" action="/admin/projekte/<?= Html::e((string) $projekt['id']) ?>/abnahme">
    <?= Csrf::feld() ?>
    <button type="submit" class="knopf">Zur Abnahme geben</button>
  </form>
<?php endif; ?>
</div>

<?php if ($stand === Projektstatus::LAUNCH_VORBEREITUNG): ?>
<div class="karte">
  <h2>Onlinegang</h2>
  <form method="post" action="/admin/projekte/<?= Html::e((string) $projekt['id']) ?>/livegang">
    <?= Csrf::feld() ?>
    <div class="feldpaar">
      <div class="feld">
        <label for="feld-live_url">Adresse der Website</label>
        <input type="url" id="feld-live_url" name="live_url" value="" placeholder="https://" required>
      </div>
      <div class="feld">
        <label for="feld-protection_started_on">Betrieb beginnt am</label>
        <input type="date" id="feld-protection_started_on" name="protection_started_on" value="">
        <p class="feld__hinweis">Leer lassen: heute. Die Mindestlaufzeit von zwölf Monaten
        rechnet das System daraus.</p>
      </div>
    </div>
    <button type="submit" class="knopf">Website ist online</button>
  </form>
</div>
<?php endif; ?>

<div class="karte">
  <h2>Erklärungen des Kunden</h2>
  <p class="leise">Nur lesbar. Eine Erklärung wird nachträglich weder geändert noch gelöscht.</p>
<?php if ($freigaben === []): ?>
  <p>Es liegt noch keine Erklärung vor.</p>
<?php else: ?>
  <ul class="liste">
<?php foreach ($freigaben as $freigabe): ?>
    <li>
      <span><?= Html::e((string) $freigabe['kind'] === 'inhalte' ? 'Fakten und Umfang freigegeben' : 'Website abgenommen') ?></span>
      <span><?= Html::e((string) $freigabe['granted_name']) ?> · <?= Html::e(Format::datumZeit((string) $freigabe['granted_at'])) ?>
        · <?= Html::e(Format::text($freigabe['granted_ip'] === null ? null : (string) $freigabe['granted_ip'])) ?></span>
    </li>
<?php endforeach; ?>
  </ul>
<?php endif; ?>
</div>

<?php if ($projekt['protection_started_on'] !== null): ?>
<div class="karte">
  <h2>Betriebsbeginn</h2>
  <ul class="pruefliste">
    <li><span>Betrieb seit</span><span><?= Html::e(Format::datum((string) $projekt['protection_started_on'])) ?></span></li>
    <li><span>Mindestlaufzeit bis</span><span><?= Html::e(Format::datum($projekt['protection_min_term_until'] === null ? null : (string) $projekt['protection_min_term_until'])) ?></span></li>
  </ul>

  <form method="post" action="/admin/projekte/<?= Html::e((string) $projekt['id']) ?>/betriebsbeginn">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-betriebsbeginn">Betriebsbeginn verschieben auf</label>
      <input type="date" id="feld-betriebsbeginn" name="protection_started_on" value="">
    </div>
    <div class="feld">
      <label for="feld-betriebsbeginn-grund">Worauf sich das stützt</label>
      <textarea id="feld-betriebsbeginn-grund" name="grund" required></textarea>
      <p class="feld__hinweis">Diese Regel muss vorher schriftlich angekündigt worden sein und
      mit der vertraglichen Formulierung übereinstimmen. Die Mindestlaufzeit rechnet sich neu.</p>
    </div>
    <button type="submit" class="knopf knopf--ruhig">Betriebsbeginn ändern</button>
  </form>
</div>
<?php endif; ?>

<div class="karte">
  <h2>Domain</h2>
  <form method="post" action="/admin/projekte/<?= Html::e((string) $projekt['id']) ?>/domain">
    <?= Csrf::feld() ?>
    <div class="feldpaar">
      <div class="feld">
        <label for="feld-desired_name">Wunschname</label>
        <input type="text" id="feld-desired_name" name="desired_name"
          value="<?= Html::e((string) ($domainstand['desired_name'] ?? '')) ?>">
      </div>
      <div class="feld">
        <label for="feld-confirmed_name">Bestätigter Name</label>
        <input type="text" id="feld-confirmed_name" name="confirmed_name"
          value="<?= Html::e((string) ($domainstand['confirmed_name'] ?? '')) ?>">
      </div>
      <div class="feld">
        <label for="feld-state">Stand</label>
        <select id="feld-state" name="state">
<?php foreach (Domainstand::zustaende() as $zustand): ?>
          <option value="<?= Html::e($zustand) ?>"<?= (string) ($domainstand['state'] ?? 'offen') === $zustand ? ' selected' : '' ?>><?= Html::e(Domainstand::kundentext($zustand)) ?></option>
<?php endforeach; ?>
        </select>
        <p class="feld__hinweis">Der Kunde liest genau diesen Satz.</p>
      </div>
    </div>

    <fieldset class="frage">
      <legend>Ist die Domain auf den Namen des Kunden registriert?</legend>
      <ul class="wahl">
        <li><input type="checkbox" id="feld-owner_confirmed" name="owner_confirmed" value="1"
          <?= (int) ($domainstand['owner_confirmed'] ?? 0) === 1 ? 'checked' : '' ?>>
          <label for="feld-owner_confirmed">Ja, auf seinen Namen</label></li>
      </ul>
    </fieldset>

    <div class="feld">
      <label for="feld-email_note">Hinweis zu den E-Mail-Adressen</label>
      <textarea id="feld-email_note" name="email_note"><?= Html::e((string) ($domainstand['email_note'] ?? '')) ?></textarea>
      <p class="feld__hinweis">Dieser Text steht im Kundenbereich.</p>
    </div>

    <div class="feld">
      <label for="feld-admin_note">Interne Notiz</label>
      <textarea id="feld-admin_note" name="admin_note"><?= Html::e((string) ($domainstand['admin_note'] ?? '')) ?></textarea>
      <p class="feld__hinweis">Nur im internen Bereich sichtbar.</p>
    </div>

    <button type="submit" class="knopf knopf--ruhig">Domainstand speichern</button>
  </form>
</div>

<div class="karte">
  <h2>Öffnungszeiten des Kunden</h2>
<?php if ($zeiten === []): ?>
  <p>Der Kunde hat noch keine Öffnungszeiten gepflegt.</p>
<?php else: ?>
<?php $wartet = false; ?>
  <ul class="liste">
<?php foreach ($zeiten as $zeit): ?>
<?php $wartet = $wartet || (int) $zeit['pending_publish'] === 1; ?>
    <li>
      <span><?= Html::e(KundenOeffnungszeiten::TAGE[(int) $zeit['weekday']] ?? '') ?><?= (int) $zeit['pending_publish'] === 1 ? ' · wartet' : '' ?></span>
      <span><?= (int) $zeit['closed'] === 1
        ? 'Geschlossen'
        : Html::e(substr((string) $zeit['open_time'], 0, 5) . ' bis ' . substr((string) $zeit['close_time'], 0, 5)) ?><?php
        if (trim((string) ($zeit['note'] ?? '')) !== ''): ?> · <?= Html::e((string) $zeit['note']) ?><?php endif; ?></span>
    </li>
<?php endforeach; ?>
<?php foreach ($zeitausnahmen as $ausnahme): ?>
    <li class="liste__unterzeile">
      <span><?= Html::e(Format::datum((string) $ausnahme['date'])) ?> · <?= Html::e(Format::text((string) $ausnahme['label'])) ?></span>
      <span><?= (int) $ausnahme['closed'] === 1
        ? 'Geschlossen'
        : Html::e(substr((string) $ausnahme['open_time'], 0, 5) . ' bis ' . substr((string) $ausnahme['close_time'], 0, 5)) ?></span>
    </li>
<?php endforeach; ?>
  </ul>

<?php if ($wartet): ?>
  <form method="post" action="/admin/projekte/<?= Html::e((string) $projekt['id']) ?>/zeiten">
    <?= Csrf::feld() ?>
    <p class="feld__hinweis">Erst auf die Website bringen, dann hier bestätigen. Der Kunde
    bekommt daraufhin die Nachricht, dass seine Änderung sichtbar ist.</p>
    <button type="submit" class="knopf knopf--ruhig">Als veröffentlicht markieren</button>
  </form>
<?php else: ?>
  <p class="leise">Es wartet keine Änderung.</p>
<?php endif; ?>
<?php endif; ?>
</div>

<div class="karte">
  <h2>Projekt anhalten</h2>
<?php if ($stand === Projektstatus::PAUSIERT): ?>
  <p>Das Projekt pausiert. Der Kunde sieht den Grund in seinem Bereich.</p>
  <form method="post" action="/admin/projekte/<?= Html::e((string) $projekt['id']) ?>/fortsetzen">
    <?= Csrf::feld() ?>
    <button type="submit" class="knopf">Projekt fortsetzen</button>
  </form>
<?php elseif ($stand !== Projektstatus::LIVE): ?>
  <form method="post" action="/admin/projekte/<?= Html::e((string) $projekt['id']) ?>/pausieren">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-grund">Grund</label>
      <textarea id="feld-grund" name="grund" required></textarea>
      <p class="feld__hinweis">Der Kunde liest den Grund. Er steht auch im Protokoll.</p>
    </div>
    <button type="submit" class="knopf knopf--ruhig">Projekt anhalten</button>
  </form>
<?php else: ?>
  <p>Eine Website, die online ist, wird nicht angehalten.</p>
<?php endif; ?>
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
