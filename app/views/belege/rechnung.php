<?php

declare(strict_types=1);

use Sartu\Helpers\Format;
use Sartu\Helpers\Html;

/**
 * Die Sichtseite einer Rechnung — `spezifikation/18_BELEGE_UND_ZAHLUNG.md` Abschnitt 2.
 *
 * **Diese Datei ist die Anzeige, nicht der Beleg.** Der Beleg ist das PDF/A-3, und was darin
 * rechtlich zaehlt, steht im eingebetteten XML. Hier steht, was ein Mensch liest.
 *
 * **Keine Zahl im Bauteil, wo eine Variable existiert.** Die Werte kommen aus
 * `design/tokens.css`; das Blatt wird beim Erzeugen eingelesen und mitgegeben, weil dompdf
 * keine Datei ueber HTTP nachlaedt.
 *
 * @var array<string,mixed> $rechnung
 * @var array<string,mixed> $betreiber
 * @var array<string,mixed> $organisation
 * @var array<string,mixed>|null $projekt
 * @var string $tokens   der Inhalt von design/tokens.css
 * @var string $logo     das Logo als data-Adresse
 * @var string $ueberschrift
 * @var list<array{bezeichnung:string,netto:int}> $positionen
 */

?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<style>
<?= $tokens ?>

/* Nur Seitenmasse und Fluss. Farben, Radien und Abstaende kommen aus tokens.css. */
@page { margin: 18mm 16mm 20mm 16mm; }

body {
  font-family: DejaVu Sans, sans-serif;
  font-size: 10pt;
  line-height: 1.45;
  color: var(--ink);
  background: #ffffff;
}

.kopf { width: 100%; margin-bottom: var(--s-5); }
.kopf td { vertical-align: top; }
.kopf__logo { width: 42mm; }
.kopf__logo img { width: 38mm; }
.kopf__absender { text-align: right; font-size: 8pt; color: var(--muted); }

.anschrift { margin-bottom: var(--s-5); }
.anschrift__vorzeile { font-size: 7pt; color: var(--muted); border-bottom: 1px solid var(--line); padding-bottom: 2mm; margin-bottom: 3mm; }

h1 { font-size: 15pt; margin: 0 0 var(--s-2); }

.eckdaten { width: 100%; margin-bottom: var(--s-4); font-size: 9pt; }
.eckdaten th { text-align: left; color: var(--muted); font-weight: normal; padding-right: 6mm; }

.posten { width: 100%; border-collapse: collapse; margin-bottom: var(--s-4); }
.posten th { text-align: left; border-bottom: 1px solid var(--line); padding: 2mm 0; font-size: 9pt; }
.posten td { padding: 2mm 0; border-bottom: 1px solid var(--line); }
.posten .betrag { text-align: right; white-space: nowrap; }

.summe { width: 62mm; margin-left: auto; border-collapse: collapse; }
.summe td { padding: 1.5mm 0; }
.summe .betrag { text-align: right; white-space: nowrap; }
.summe .gesamt td { border-top: 1px solid var(--line); font-weight: bold; padding-top: 2.5mm; }

.hinweis { margin-top: var(--s-4); font-size: 9pt; }
.fuss { margin-top: var(--s-5); padding-top: 3mm; border-top: 1px solid var(--line); font-size: 7.5pt; color: var(--muted); }
.fuss td { vertical-align: top; width: 33%; }
</style>
</head>
<body>

<table class="kopf">
  <tr>
    <td class="kopf__logo"><img src="<?= Html::e($logo) ?>" alt="SARTU"></td>
    <td class="kopf__absender">
      <?= Html::e(Format::text((string) ($betreiber['firmenname'] ?? ''))) ?><br>
      <?= Html::e(Format::text((string) ($betreiber['strasse'] ?? ''))) ?><br>
      <?= Html::e(trim((string) ($betreiber['plz'] ?? '') . ' ' . (string) ($betreiber['ort'] ?? ''))) ?><br>
      <?= Html::e(Format::text((string) ($betreiber['email'] ?? ''))) ?>
    </td>
  </tr>
</table>

<div class="anschrift">
  <p class="anschrift__vorzeile">
    <?= Html::e(Format::text((string) ($betreiber['firmenname'] ?? ''))) ?> ·
    <?= Html::e(Format::text((string) ($betreiber['strasse'] ?? ''))) ?> ·
    <?= Html::e(trim((string) ($betreiber['plz'] ?? '') . ' ' . (string) ($betreiber['ort'] ?? ''))) ?>
  </p>
  <p>
    <strong><?= Html::e(Format::text((string) ($organisation['legal_name'] ?? ''))) ?></strong><br>
    <?= Html::e(Format::text($organisation['street'] ?? null)) ?><br>
    <?= Html::e(trim((string) ($organisation['postal_code'] ?? '') . ' ' . (string) ($organisation['city'] ?? ''))) ?>
  </p>
</div>

<h1><?= Html::e($ueberschrift) ?> <?= Html::e((string) $rechnung['number']) ?></h1>

<table class="eckdaten">
  <tr>
    <th>Rechnungsdatum</th><td><?= Html::e(Format::datum($rechnung['issued_at'] === null ? null : (string) $rechnung['issued_at'])) ?></td>
    <th>Zahlbar bis</th><td><?= Html::e(Format::datum($rechnung['due_date'] === null ? null : (string) $rechnung['due_date'])) ?></td>
  </tr>
  <tr>
    <th>Steuernummer</th><td><?= Html::e(Format::text((string) ($betreiber['steuernummer'] ?? ''))) ?></td>
    <th>Projekt</th><td><?= Html::e(Format::text($projekt === null ? null : (string) $projekt['title'])) ?></td>
  </tr>
</table>

<table class="posten">
  <thead>
    <tr><th>Leistung</th><th class="betrag">Netto</th></tr>
  </thead>
  <tbody>
<?php foreach ($positionen as $position): ?>
    <tr>
      <td><?= Html::e($position['bezeichnung']) ?></td>
      <td class="betrag"><?= Html::e(Format::euro($position['netto'])) ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>

<table class="summe">
  <tr>
    <td>Netto</td>
    <td class="betrag"><?= Html::e(Format::euro((int) $rechnung['net_cents'])) ?></td>
  </tr>
  <tr>
    <td>Umsatzsteuer 19 %</td>
    <td class="betrag"><?= Html::e(Format::euro((int) $rechnung['vat_cents'])) ?></td>
  </tr>
  <tr class="gesamt">
    <td>Gesamtbetrag</td>
    <td class="betrag"><?= Html::e(Format::euro((int) $rechnung['gross_cents'])) ?></td>
  </tr>
</table>

<p class="hinweis"><?= Html::e($hinweis) ?></p>

<table class="fuss">
  <tr>
    <td>
      <?= Html::e(Format::text((string) ($betreiber['firmenname'] ?? ''))) ?><br>
      <?= Html::e(Format::text((string) ($betreiber['strasse'] ?? ''))) ?><br>
      <?= Html::e(trim((string) ($betreiber['plz'] ?? '') . ' ' . (string) ($betreiber['ort'] ?? ''))) ?>
    </td>
    <td>
      <?= Html::e(Format::text((string) ($betreiber['telefon'] ?? ''))) ?><br>
      <?= Html::e(Format::text((string) ($betreiber['email'] ?? ''))) ?>
    </td>
    <td>
      Steuernummer <?= Html::e(Format::text((string) ($betreiber['steuernummer'] ?? ''))) ?><br>
      <?= Html::e(Format::text((string) ($betreiber['bank_institut'] ?? ''))) ?><br>
      <?= Html::e(Format::text((string) ($betreiber['bank_iban'] ?? ''))) ?>
    </td>
  </tr>
</table>

</body>
</html>
