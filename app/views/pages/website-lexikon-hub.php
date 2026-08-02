<?php

declare(strict_types=1);

use Sartu\Helpers\Html;
use Sartu\Services\Lexikon;

/**
 * `/lexikon` — Website-Lastenheft §13, Hub.
 *
 * **Kein Suchfeld bei acht Begriffen** (§13, erst ab etwa 40). Eine Suche über acht Zeilen
 * kostet einen Klick und spart keinen.
 *
 * Alphabetisch sortiert, wie §13 es verlangt — nach dem Begriff, nicht nach dem Schlüssel.
 *
 * @var list<array{schluessel:string,begriff:string,kurz:string}> $begriffe
 */

?>
<section class="aufmacher">
  <div class="bahn schmal">
    <h1><?= Html::e(Lexikon::HUB_H1) ?></h1>
    <p class="lede"><?= Html::e(Lexikon::HUB_INTRO) ?></p>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn">
    <ul class="leistungszeilen">
<?php foreach ($begriffe as $eintrag): ?>
      <li>
        <h2><a href="/lexikon/<?= Html::e($eintrag['schluessel']) ?>"><?= Html::e($eintrag['begriff']) ?></a></h2>
        <p><?= Html::e($eintrag['kurz']) ?></p>
      </li>
<?php endforeach; ?>
    </ul>
  </div>
</section>
