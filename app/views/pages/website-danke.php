<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/**
 * Die Bestätigung nach dem Kontaktformular — Website-Lastenheft §11 und §14.
 *
 * §14: „Danke-Seiten: `noindex`, klare nächste Erwartung, **keine weiteren Angebote**."
 * Deshalb steht hier kein Knopf zum Bedarfsscheck und keine Preiszeile. Wer gerade
 * geschrieben hat, bekommt keine zweite Aufforderung.
 *
 * @var string $satz
 */

?>
<section class="abschnitt">
  <div class="bahn schmal">
    <h1>Ihre Nachricht ist angekommen.</h1>
    <p class="lede"><?= Html::e($satz) ?></p>
    <p><a class="textlink" href="/">Zurück zur Startseite</a></p>
  </div>
</section>
