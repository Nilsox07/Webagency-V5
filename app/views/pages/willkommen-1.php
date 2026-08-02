<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/**
 * Willkommensstrecke, Bildschirm 1 von 3 — Portal-Lastenheft §7, Wortlaut gebunden.
 *
 * **Eine Abweichung:** §7 schreibt „Das ist Ihr Projektportal." Nach aussen heisst der
 * Bereich Kundenbereich (`CLAUDE.md`, Website-Lastenheft §5b). Der Satz lautet deshalb
 * „Das ist Ihr Kundenbereich." — der Rest steht unveraendert.
 *
 * §7: „Kein Zwang. Wer `Überspringen` klickt, kann alles trotzdem uneingeschraenkt bedienen."
 *
 * @var string $vorname
 */

?>
<p class="vorzeile">Bildschirm 1 von 3</p>
<h1>Willkommen bei SARTU<?= $vorname === '' ? '' : ', ' . Html::e($vorname) ?>.</h1>

<p class="lead">Das ist Ihr Kundenbereich. Hier läuft alles zu Ihrer Website an einem Ort:
Angebot, Zahlung, offene Aufgaben, Vorschau und später kleine Änderungen.</p>

<p>Keine E-Mail-Suche, keine verlorenen Anhänge, kein Rätselraten, wie weit das Projekt ist.</p>

<div class="knopfreihe">
  <a class="knopf" href="/willkommen/2">Weiter</a>
</div>

<?= \Sartu\Ansicht::teil('partials/willkommen-ueberspringen') ?>
