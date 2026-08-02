<?php

declare(strict_types=1);

use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Preise;

/**
 * `/portal/angebot` — Portal-Lastenheft §8.2.
 *
 * Zeigt **alle** Felder aus `offers` in der dort vorgegebenen Reihenfolge. Ein Angebot ist
 * die vertragliche Grundlage; was hier fehlt, fehlt spaeter im Streitfall.
 *
 * **Der Annahmeblock ist noch nicht gebaut.** Die Annahme ist ein Zustandswechsel mit
 * Anzahlungsrechnung dahinter (§5.1a) und gehoert damit zu A2 (`REIHENFOLGE.md`, Testfaelle
 * 11 bis 13). Statt einer gesperrten Schaltflaeche steht ein Satz, der sagt, was als
 * Naechstes passiert — §0.3b verbietet ausgegraute Knoepfe.
 *
 * @var array<string,mixed>|null $angebot
 * @var array<string,mixed>|null $preise
 */

$zahlungsplan = [
    '50_50'    => '50 % bei Auftrag, 50 % nach Abnahme vor dem Onlinegang. Zahlungsziel jeweils 10 Kalendertage.',
    '40_30_30' => '40 % bei Auftrag, 30 % nach der ersten Vorschau, 30 % nach Abnahme vor dem Onlinegang. '
        . 'Zahlungsziel jeweils 10 Kalendertage.',
];

?>
<h1>Ihr Angebot</h1>

<?php if ($angebot === null): ?>
<div class="karte">
  <p>Sobald wir Ihre Anfrage geprüft haben, erscheint hier Ihr Angebot mit Umfang, Preis und
  Zahlungsplan.</p>
</div>
<?php else: ?>
<?php
$einmalig = (int) $angebot['one_time_net_cents'];
$ust = (int) round($einmalig * Preise::UST_PROZENT / 100);
?>

<div class="karte">
  <ul class="pruefliste">
    <li><span>Angebotsnummer</span><span><?= Html::e((string) $angebot['number']) ?></span></li>
    <li><span>Gültig bis</span><span><?= Html::e(Format::datum((string) $angebot['valid_until'])) ?></span></li>
  </ul>
</div>

<div class="karte">
  <h2>Ihr Ziel und unsere Empfehlung</h2>
  <p><?= nl2br(Html::e((string) $angebot['summary'])) ?></p>
  <p><strong>Empfohlener Umfang: <?= Html::e(Preise::name((string) $angebot['package'])) ?></strong></p>
</div>

<div class="karte">
  <h2>Vorgesehene Seitenstruktur</h2>
  <p><?= nl2br(Html::e((string) $angebot['sitemap'])) ?></p>
</div>

<div class="karte">
  <h2>Was enthalten ist</h2>
  <p><?= nl2br(Html::e((string) $angebot['inclusions'])) ?></p>
  <h3>Was nicht enthalten ist</h3>
  <p><?= nl2br(Html::e((string) $angebot['exclusions'])) ?></p>
</div>

<div class="karte">
  <h2>Umfang und Korrekturrunden</h2>
  <ul class="pruefliste">
    <li><span>Umfangsgrenze</span><span><?= Html::e(
        $angebot['scope_pages'] === null && $angebot['scope_words'] === null
            ? Format::LEER
            : $angebot['scope_pages'] . ' Seiten, rund ' . number_format((int) $angebot['scope_words'], 0, ',', '.') . ' Wörter'
    ) ?></span></li>
    <li><span>Korrekturrunden</span><span><?= Html::e((string) $angebot['included_feedback_rounds']) ?> enthaltene Korrekturrunden</span></li>
  </ul>
  <p>Umfang darüber hinaus bieten wir Ihnen vorher getrennt an.</p>
  <p>Eine Korrekturrunde bedeutet: Sie sammeln alle Anmerkungen und reichen sie gebündelt ein,
  wir arbeiten sie in einem Durchgang ein.</p>
</div>

<div class="karte">
  <h2>Zeitrahmen</h2>
  <p>Fertigstellung in <?= Html::e((string) $angebot['delivery_days_min']) ?>–<?= Html::e((string) $angebot['delivery_days_max']) ?> Werktagen.</p>
  <p><?= nl2br(Html::e((string) $angebot['delivery_start_condition'])) ?></p>
</div>

<div class="karte">
  <h2>Preis</h2>
  <ul class="pruefliste">
    <li><span>Einmalpreis netto</span><span><?= Html::e(Format::euro($einmalig)) ?></span></li>
    <li><span>Umsatzsteuer <?= Html::e((string) Preise::UST_PROZENT) ?> %</span><span><?= Html::e(Format::euro($ust)) ?></span></li>
    <li><span>Bruttobetrag</span><span><?= Html::e(Format::euro($einmalig + $ust)) ?></span></li>
    <li><span>Monatlicher Betrieb netto</span><span><?= Html::e(Format::euro((int) $angebot['protection_monthly_net_cents'])) ?></span></li>
    <li><span>Mindestlaufzeit</span><span><?= Html::e((string) $angebot['protection_min_term_months']) ?> Monate</span></li>
    <li><span>Erstjahreswert netto</span><span><?= Html::e(Format::euro((int) $angebot['first_year_net_cents'])) ?></span></li>
  </ul>
</div>

<div class="karte">
  <h2>Zahlungsplan</h2>
<?php if ((string) $angebot['payment_plan'] === 'custom'): ?>
  <p><?= nl2br(Html::e((string) $angebot['payment_plan_custom'])) ?></p>
  <p>Zahlungsziel jeweils 10 Kalendertage.</p>
<?php else: ?>
  <p><?= Html::e($zahlungsplan[(string) $angebot['payment_plan']] ?? '') ?></p>
<?php endif; ?>
</div>

<div class="karte">
  <h2>Rechte und Export</h2>
  <p><?= nl2br(Html::e((string) $angebot['rights_text'])) ?></p>
</div>

<div class="karte">
  <h2>Domain und E-Mail</h2>
  <p><?= nl2br(Html::e((string) $angebot['domain_text'])) ?></p>
</div>

<?php if ((string) $angebot['bfsg_vertragsabschluss'] !== ''): ?>
<div class="karte">
  <h2>Ihre Angaben zur Barrierefreiheit</h2>
  <p class="leise">Es sind Ihre Angaben, keine Feststellung von SARTU.</p>
  <ul class="pruefliste">
    <li><span>Besucher schließen über die Seite einen Vertrag ab</span><span><?= Html::e(ucfirst((string) $angebot['bfsg_vertragsabschluss'])) ?></span></li>
    <li><span>Weniger als 10 Beschäftigte und höchstens 2 Mio. € Umsatz</span><span><?= Html::e(ucfirst((string) $angebot['bfsg_kleinstunternehmen'])) ?></span></li>
  </ul>
</div>
<?php endif; ?>

<p class="fussnote">Alle Preise netto zzgl. gesetzlicher Umsatzsteuer. Ausschließlich für
Unternehmer.</p>
<p class="fussnote">Zur Beauftragung melden wir uns bei Ihnen. Sie müssen jetzt nichts tun.</p>
<?php endif; ?>
