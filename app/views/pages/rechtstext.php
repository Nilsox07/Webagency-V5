<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/**
 * Ein Rechtstext — Impressum, Datenschutz, AGB.
 *
 * ## Was am 16.08.2026 geändert wurde
 *
 * Vorher lief der ganze Rumpf durch `preg_split` auf Leerzeilen und wurde als Folge von
 * `<p>` ausgegeben. Die Entwürfe in `rechtstexte-entwuerfe/` sind aber gegliedert — sie
 * tragen `#`, `##` und `###`. Auf der Seite stand deshalb wörtlich `## Anbieter` als
 * Fliesstext.
 *
 * Drei Folgen, alle gemessen:
 *
 * | Was | Warum es zählt |
 * |---|---|
 * | keine Überschriftenebenen | Ein Vorleseprogramm kann den Text nicht überfliegen; § 5 DDG verlangt „leicht erkennbar" |
 * | Auszeichnungsreste sichtbar | `OberflaecheTest::testKeinAuszeichnungsrestImAusgeliefertenText` prüft genau darauf — die Rechtsseiten entgingen ihm nur, weil sie 404 liefern |
 * | kein Sprungziel | Ein Datenschutztext von 40 Abschnitten ist ohne Marken nicht benutzbar |
 *
 * **Der Text selbst wird nicht verändert.** Der Renderer erkennt Struktur, er schreibt
 * nicht um: Was im Entwurf steht, steht auf der Seite. Ein Rechtstext wird von einer
 * qualifizierten Stelle freigegeben, nicht von einer Ansicht umformuliert.
 *
 * @var string $beschriftung
 * @var string $rumpf
 * @var string|null $stand
 */

/**
 * Eine Überschriftenzeile? Dann Ebene und Text, sonst null.
 *
 * `#` wird zu `h2` und nicht zu `h1`: Die H1 der Seite steht darüber und ist die
 * Beschriftung aus `RechtstexteSpeicher`. Ein zweites `h1` im Rumpf wäre ein Sprung in der
 * Gliederung — `MarkupTest` prüft die Ebenenfolge.
 */
$ueberschrift = static function (string $zeile): ?array {
    if (preg_match('/^(#{1,4})\s+(.+)$/u', trim($zeile), $treffer) !== 1) {
        return null;
    }

    return [min(4, strlen($treffer[1]) + 1), trim($treffer[2])];
};

/** Eine Sprungmarke aus der Überschrift — kleingeschrieben, ohne Sonderzeichen. */
$marke = static function (string $text): string {
    $roh = strtr(mb_strtolower($text, 'UTF-8'), ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);

    return trim((string) preg_replace('/[^a-z0-9]+/u', '-', $roh), '-');
};

$abschnitte = preg_split('/\R{2,}/', trim($rumpf)) ?: [];

/*
 * Das Inhaltsverzeichnis entsteht **nur bei langen Texten**. Bei einem Impressum mit sechs
 * Abschnitten steht es im Weg; bei einer Datenschutzerklärung mit dreissig ist es der einzige
 * Weg, die eine gesuchte Angabe zu finden.
 */
$marken = [];

foreach ($abschnitte as $abschnitt) {
    $kopf = $ueberschrift($abschnitt);

    if ($kopf !== null && $kopf[0] === 2) {
        $marken[$marke($kopf[1])] = $kopf[1];
    }
}

?>
<p class="vorzeile">Rechtstext</p>
<h1><?= Html::e($beschriftung) ?></h1>

<?php if (($stand ?? null) !== null): ?>
<p class="dokument__stand">Stand: <?= Html::e((string) $stand) ?></p>
<?php endif; ?>

<?php if (count($marken) >= 6): ?>
<nav class="dokument__marken" aria-label="Abschnitte dieses Textes">
  <ul>
<?php foreach ($marken as $ziel => $wort): ?>
    <li><a href="#<?= Html::e($ziel) ?>"><?= Html::e($wort) ?></a></li>
<?php endforeach; ?>
  </ul>
</nav>
<?php endif; ?>

<?php foreach ($abschnitte as $abschnitt): ?>
<?php $kopf = $ueberschrift($abschnitt); ?>
<?php if ($kopf !== null): ?>
<?php $stufe = (int) $kopf[0]; ?>
<h<?= $stufe ?><?= $stufe === 2 ? ' id="' . Html::e($marke($kopf[1])) . '"' : '' ?>><?= Html::e($kopf[1]) ?></h<?= $stufe ?>>
<?php else: ?>
<p><?= nl2br(Html::e(trim($abschnitt))) ?></p>
<?php endif; ?>
<?php endforeach; ?>
