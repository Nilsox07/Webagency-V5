<?php

declare(strict_types=1);

use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Ratgeber;

/**
 * `/ratgeber` — Website-Lastenheft §12, Hub.
 *
 * **Kein Kategorienfilter bei wenigen Artikeln** (§12). Fünf Einträge brauchen keine
 * Sortierung, sie brauchen eine Liste.
 *
 * Der Hub listet Ratgeber **und** Transparenzseiten, „weil sie für Leser dasselbe sind".
 *
 * @var array<string,string>|null $auftragslage
 * @var string $preishinweis
 */

?>
<section class="aufmacher">
  <div class="bahn schmal">
    <h1><?= Html::e(Ratgeber::HUB_H1) ?></h1>
    <p class="lede"><?= Html::e(Ratgeber::HUB_INTRO) ?></p>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn">
    <ul class="leistungszeilen">
<?php foreach (Ratgeber::alle() as $schluessel => $artikel): ?>
      <li>
        <h2><a href="/ratgeber/<?= Html::e($schluessel) ?>"><?= Html::e($artikel['h1']) ?></a></h2>
        <p><?= Html::e($artikel['kurzantwort']) ?></p>
        <p class="marken">Stand <?= Html::e(Format::datum(Ratgeber::STAND)) ?></p>
      </li>
<?php endforeach; ?>
    </ul>
  </div>
</section>
