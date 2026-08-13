<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/**
 * Open Graph und `twitter:card` — die Vorschaukarte beim Teilen.
 *
 * **Bis zum 13.08.2026 fehlten beide auf jeder Seite.** Wer eine SARTU-Adresse in WhatsApp,
 * LinkedIn oder Slack einfügte, bekam den blanken Link: kein Titel, kein Bild, keine
 * Beschreibung. Die Angaben stehen alle schon im Dokument — sie waren nur nicht in der
 * Form da, die ein Vorschaudienst liest.
 *
 * ## Warum die Werte nicht eigens getextet sind
 *
 * `og:title` und `og:description` übernehmen `<title>` und `<meta name="description">`.
 * Eine zweite Fassung wäre eine zweite Wahrheit über dieselbe Seite — und die
 * auseinandergelaufene von beiden ist immer die, die niemand liest. §17 verlangt je Seite
 * einen eigenen Titel und eine eigene Beschreibung; damit sind beide ohnehin schon
 * seitenspezifisch.
 *
 * ## Das Bild
 *
 * `sartu-og.png`, 1200 × 630 — die Größe, die Facebook, LinkedIn und X gemeinsam
 * verarbeiten. Es trägt die ausgelieferte Wortmarke auf `--ink` und **keinen Text**: Ein
 * Preis oder ein Versprechen im Bild veraltet, ohne dass es jemandem auffällt, und §17
 * verbietet Zahlen ohne Quelle an jeder Stelle — auch in einem Bild.
 *
 * `twitter:card` steht auf `summary_large_image`, weil es das Bild groß zeigt. Bei
 * `summary` erschiene die Marke als Briefmarke neben dem Text.
 *
 * **Kein `og:image:alt` mit erfundenem Inhalt:** Die Beschreibung nennt, was zu sehen ist —
 * die Wortmarke. Mehr ist nicht drauf.
 *
 * @var string $titel
 * @var string|null $beschreibung
 * @var string $adresse  die vollständige Adresse dieser Seite (dieselbe wie `canonical`)
 * @var string $basis    die Adresse der Website ohne Schrägstrich am Ende
 */

$beschreibung = $beschreibung ?? null;

?>
<meta property="og:type" content="website">
<meta property="og:site_name" content="SARTU">
<meta property="og:locale" content="de_DE">
<meta property="og:title" content="<?= Html::e($titel) ?>">
<meta property="og:url" content="<?= Html::e($adresse) ?>">
<meta property="og:image" content="<?= Html::e($basis . '/assets/bild/sartu-og.png') ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="Die Wortmarke SARTU auf dunklem Grund.">
<?php if ($beschreibung !== null): ?>
<meta property="og:description" content="<?= Html::e($beschreibung) ?>">
<?php endif; ?>
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= Html::e($titel) ?>">
<meta name="twitter:image" content="<?= Html::e($basis . '/assets/bild/sartu-og.png') ?>">
<?php if ($beschreibung !== null): ?>
<meta name="twitter:description" content="<?= Html::e($beschreibung) ?>">
<?php endif; ?>
