<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/**
 * Ein Einwandabschnitt — Website-Lastenheft §5 Sektion 9 und die FAQ-Blöcke der Unterseiten.
 *
 * **`<details>` statt Akkordeon mit Skript.** §1 verlangt volle Nutzbarkeit ohne JavaScript;
 * ein selbstgebautes Akkordeon wäre ohne Skript zu. `<details>` öffnet, schließt, ist über
 * die Tastatur bedienbar und wird von Vorlesesoftware als aufklappbarer Bereich angesagt —
 * ohne eine Zeile Code.
 *
 * **Die Frage steht als Frage da, wörtlich.** Der Texter-Skill zu GEO: „Fragen wörtlich
 * stellen und direkt beantworten." Eine umgeschriebene Überschrift beantwortet die
 * Suchanfrage nicht mehr.
 *
 * **Kein `FAQPage`-Schema.** §16: Google hat FAQ-Rich-Results eingestellt; das Markup
 * schadet nicht, bringt aber nichts — und wer es ausliefert, darf es nicht als Maßnahme
 * führen. Die Blöcke selbst bleiben Pflicht, als Inhalt für Leser.
 *
 * @var list<array{frage:string,antwort:string}> $fragen
 */

?>
<div class="fragen">
<?php foreach ($fragen as $eintrag): ?>
  <details>
    <summary><?= Html::e($eintrag['frage']) ?></summary>
    <p><?= Html::e($eintrag['antwort']) ?></p>
  </details>
<?php endforeach; ?>
</div>
