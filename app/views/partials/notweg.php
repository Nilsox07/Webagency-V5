<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/**
 * Der Notweg aus Portal-Lastenheft §6.3.
 *
 * > „Der Anmeldelink ist der einzige Weg ins Portal. Kommt die Mail nicht an […], ist der
 * > Kunde **ausgesperrt**. Und er kann es niemandem melden, weil der Meldeweg selbst im
 * > Portal liegt."
 *
 * Die Werte kommen aus den Betreiberdaten. Steht dort nichts, steht hier nichts — eine
 * erfundene Nummer waere schlimmer als keine (Testfall 83).
 *
 * @var array{telefon:?string,email:?string} $notweg
 */

if ($notweg['telefon'] === null && $notweg['email'] === null) {
    return;
}

?>
<div class="meldung meldung--hinweis">
  <p><strong>Kommt der Link nicht an?</strong></p>
  <p>
<?php if ($notweg['telefon'] !== null): ?>
    Rufen Sie uns an: <?= Html::e($notweg['telefon']) ?>.
<?php endif; ?>
<?php if ($notweg['email'] !== null): ?>
    Oder schreiben Sie an <a href="mailto:<?= Html::e($notweg['email']) ?>"><?= Html::e($notweg['email']) ?></a>.
<?php endif; ?>
    Wir richten Ihnen den Zugang von Hand ein.
  </p>
</div>
