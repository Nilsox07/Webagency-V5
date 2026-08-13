<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/**
 * Das Bild im Aufmacher.
 *
 * ## Was sich am 13.08.2026 geändert hat
 *
 * Bis dahin stand hier ein **selbst gezeichneter** Laptop mit angeschnittenem Telefon —
 * `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4b, entschieden am 10.08.2026. Die Anweisung vom
 * 13.08.2026 hebt das auf: Der gezeichnete Rahmen wird durch ein fertig gerendertes Mockup
 * ersetzt, `public/assets/bild/geraet-aufmacher.webp`, und der dann tote CSS-Block wird
 * gelöscht statt liegen gelassen.
 *
 * **Das Mockup liegt nicht im Repository.** Es entsteht außerhalb, in einem Mockup-Werkzeug.
 * Gelöscht ist der Rahmen trotzdem — die Anweisung wurde zweimal bestätigt.
 *
 * ## Was hier steht, solange das Mockup fehlt
 *
 * Die **echte Aufnahme des eigenen Kundenbereichs** mit Musterdaten. Sie liegt seit dem
 * 10.08.2026 im Repository und ist der Kern der Entscheidung §4b: „Auf dem Bildschirm eine
 * echte Aufnahme des eigenen Kundenbereichs. Keine nachgezeichnete Oberfläche."
 *
 * **Erfunden ist damit nichts** — das Verbot des Auftrags („erfinde kein Ersatzbild") ist
 * eingehalten. Es fehlt allein das Gehäuse um eine Aufnahme, die es gibt.
 *
 * ## Der Tausch geschieht hier von selbst
 *
 * Liegt das Mockup, wird es genommen. `MarkupTest::testDerGeraeterahmenWirdGetauschtSobaldDasMockupVorliegt`
 * hält beide Zustände fest, damit weder das eine noch das andere vergessen wird.
 *
 * ## Der Vermerk steht neben dem Bild
 *
 * `Musteransicht` ist gebunden (§5 Sektion 1, „Visual rechts mit Kennzeichen"). Bis zum
 * 13.08.2026 lag er **auf** dem Bild; der Auftrag verlangt ausdrücklich „neben dem Bild,
 * nicht darauf". Er steht deshalb als `figcaption` darunter — im selben Blick, aber ohne
 * die Aufnahme zu verdecken.
 *
 * @var string $marke der gebundene Vermerk
 */

$mockup = '/assets/bild/geraet-aufmacher.webp';
$vorhanden = is_file(dirname(__DIR__, 3) . '/public' . $mockup);

$quelle = $vorhanden ? $mockup : '/assets/bild/sartu-kundenbereich-muster.webp';

$beschreibung = $vorhanden
    ? 'Der Kundenbereich auf einem Laptop: drei offene Aufgaben und der Projektstand.'
    : 'Der Kundenbereich mit einer Musteransicht: drei offene Aufgaben und der Projektstand.';

?>
<figure class="geraet">
  <div class="geraet__schirm">
    <img src="<?= Html::e($quelle) ?>"
         alt="<?= Html::e($beschreibung) ?>"
         width="1600" height="1040" loading="eager" decoding="async">
  </div>

  <figcaption class="geraet__marke"><?= Html::e($marke) ?></figcaption>
</figure>
