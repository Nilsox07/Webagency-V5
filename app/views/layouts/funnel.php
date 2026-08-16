<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Env;
use Sartu\Helpers\Html;

/**
 * Das Layout des Bedarfsschecks — die **transaktionale** Seitengattung.
 *
 * ## Warum es dieses Layout gibt
 *
 * Bis zum 16.08.2026 lief der ganze Bedarfsscheck über `layouts/oeffentlich` mit
 * `anwendung.css`. Das ist das Gerüst der **internen** Bildschirme: eine 46-rem-Spalte,
 * mittig, ohne Kopf, mit einer Fusszeile aus zwei Verweisen. Ergebnis auf
 * `/briefing/ergebnis`: eine schmale Insel in sehr viel leerer Fläche, eine grosse hellgrüne
 * Rundkarte, drei weisse Pillen für gewöhnliche Fakten — der Bildschirm, an dem die
 * Kaufentscheidung fällt, sah aus wie ein fremdes Formular.
 *
 * ## Was diese Gattung von den anderen unterscheidet
 *
 * | | Marketing (`website`) | **Funnel (hier)** | Dokument (`dokument`) | Anwendung (`portal`) |
 * |---|---|---|---|---|
 * | Kopf | volle Navigation | **Marke und ein Weg zurück** | Marke und Navigation | Seitenleiste |
 * | Aufmacher | Bänder, Gerät, H1 | **keiner** | keiner | keiner |
 * | Fuss | vollständig | **kompakt, mit Rechtstexten** | vollständig | keiner |
 * | Satzspiegel | `--wrap` | **`--wrap`, Inhalt asymmetrisch darin** | 65–75 Zeichen | dicht |
 *
 * **Keine vollständige Marketingnavigation im Formular.** Wer den Bedarfsscheck begonnen
 * hat, soll ihn zu Ende führen können — sieben Navigationspunkte daneben sind sieben
 * Ausstiege. Ein Weg zurück zur Website bleibt, weil ein Formular ohne Ausgang eine Falle
 * ist.
 *
 * **Der Fuss ist kompakt und trotzdem vollständig.** Impressum und Datenschutz müssen von
 * jeder Seite erreichbar sein; der Pflichthinweis zu den Preisen gehört dorthin, wo Preise
 * stehen. Beides passt in drei Zeilen.
 *
 * @var string $titel
 * @var string $inhalt
 * @var string|null $beschreibung
 * @var string|null $pfad
 * @var bool $noindex
 * @var int|null $schritt        die Nummer des laufenden Schritts, sonst null
 * @var int|null $schritte       wie viele es insgesamt sind
 */

$pfad = $pfad ?? null;
$basis = rtrim((string) Env::get('BASE_URL', ''), '/');

?><!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= Html::e($titel) ?> | SARTU</title>
<?php if (isset($beschreibung) && $beschreibung !== null): ?>
<meta name="description" content="<?= Html::e($beschreibung) ?>">
<?php endif; ?>
<?php /* §9: Der Einstieg ist indexierbar, jeder Folgeschritt traegt `noindex`. */ ?>
<?php if (($noindex ?? false) === true): ?>
<meta name="robots" content="noindex, follow">
<?php endif; ?>
<?php if ($pfad !== null): ?>
<link rel="canonical" href="<?= Html::e($basis . $pfad) ?>">
<?php endif; ?>
<?= Ansicht::teil('partials/markenzeichen') ?>
<?= Ansicht::teil('partials/teilenkarte', [
    'titel'        => $titel . ' | SARTU',
    'beschreibung' => $beschreibung ?? null,
    'adresse'      => $basis . ($pfad ?? '/briefing'),
    'basis'        => $basis,
]) ?>
<?php /* **Vier Dateien, in dieser Reihenfolge.** `anwendung.css` ist trotz seines Namens
         die **gemeinsame Grundlage** — Grundschrift, Knopf, Feld, Fokus, Meldung. Das
         Website-Layout bindet es ebenso ein. Der Bedarfsscheck kann darauf nicht
         verzichten; was er nicht braucht, ist der **Zuschnitt** des internen Bereichs, und
         den hebt `bedarfsscheck.css` auf. */ ?>
<link rel="stylesheet" href="/assets/css/tokens.css">
<link rel="stylesheet" href="/assets/css/anwendung.css">
<link rel="stylesheet" href="/assets/css/website.css">
<link rel="stylesheet" href="/assets/css/bedarfsscheck.css">
</head>
<body class="funnelseite">
<a class="sprungmarke" href="#inhalt">Zum Inhalt springen</a>

<?php /* Der schlanke Kopf: Marke links, ein Weg zurück rechts. Kein Menü, keine sieben
         Punkte — und deshalb auch kein `menue.js`. */ ?>
<header class="funnelkopf">
  <div class="bahn funnelkopf__reihe">
    <a class="seitenmarke" href="/">
      <img src="/assets/bild/sartu-logo-hell.svg" alt="SARTU" width="181" height="34">
    </a>
    <a class="textlink" href="/">Zurück zur Website</a>
  </div>
</header>

<main id="inhalt" class="funnelflaeche">
<?php if (isset($schritt, $schritte) && $schritt !== null && $schritte !== null): ?>
  <?php /* **Eine Zeile und ein Balken, keine fünf Pillen.** Der Fortschritt ist eine
           Angabe, keine Auswahl: Man kann ihn nicht anklicken, und fünf runde Marken
           daneben sähen wie eine Navigation aus, die es nicht gibt.

           **`Thema`, nicht `Schritt`.** Der Auftrag vom 16.08.2026 nennt „Schritt n von 5";
           `17_SEITEN_SARTU.md` §9.1 bindet „Thema 1 von 5" und begründet es („nicht Frage 1
           von 10"). Der gebundene Wortlaut gewinnt; die Abweichung steht in
           `OFFENE_PRUEFUNGEN.md`.

           Der Balken ist `aria-hidden` — die Zeile daneben sagt dasselbe in Worten. Seine
           Breite kommt als `data-anteil`, nicht als `style`: Die eigene CSP führt
           `style-src 'self'` ohne `unsafe-inline`. */ ?>
  <div class="bahn funnelbahn">
    <p class="funnelschritt">
      <span class="funnelschritt__wort">Thema <?= (int) $schritt ?> von <?= (int) $schritte ?></span>
      <span class="funnelschritt__balken" data-anteil="<?= (int) $schritt ?>" aria-hidden="true"></span>
    </p>
  </div>
<?php endif; ?>

<?= $inhalt ?>
</main>

<?php /* Der kompakte Fuss. Er trägt, was jede Seite tragen muss: die Rechtstexte und den
         Pflichthinweis zu den Preisen. Mehr nicht — der Bedarfsscheck ist kein Ort, an dem
         jemand die Leistungsübersicht sucht. */ ?>
<footer class="funnelfuss">
  <div class="bahn funnelfuss__reihe">
    <p class="funnelfuss__recht">
      <a href="/impressum">Impressum</a> · <a href="/datenschutz">Datenschutz</a>
    </p>
    <p class="funnelfuss__hinweis">Alle Preise netto zzgl. gesetzlicher Umsatzsteuer.
      Ausschließlich für Unternehmer.</p>
  </div>
</footer>
</body>
</html>
