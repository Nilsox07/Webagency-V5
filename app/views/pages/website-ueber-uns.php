<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Html;
use Sartu\Services\Firmenseitentexte as T;
use Sartu\Services\Websitetexte;

/**
 * `/ueber-uns` — Website-Lastenheft §11.
 *
 * **Ohne Hero-Foto und ohne Gründernamen.** Begründung im Kopf von `Firmenseitentexte`.
 * §11 nennt die Sektion mit echtem Foto; das Foto steht auf `offen`, und ein Platzhalter,
 * der wie ein Foto wirkt, ist ausdrücklich unzulässig.
 *
 * @var array<string,string>|null $auftragslage
 * @var string $preishinweis
 */

?>
<section class="aufmacher">
  <div class="bahn schmal">
    <h1><?= Html::e(T::UEBER_H1) ?></h1>
    <p class="lede"><?= Html::e(T::UEBER_LEAD) ?></p>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn">
    <h2>Vier Dinge machen wir anders.</h2>

    <ul class="leistungszeilen">
<?php foreach (T::warumAnders() as $punkt): ?>
      <li>
        <h3><?= Html::e($punkt['titel']) ?></h3>
        <p><?= Html::e($punkt['text']) ?></p>
      </li>
<?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn schmal">
    <h2>Was SARTU bewusst nicht ist.</h2>
    <ul class="hakenliste">
<?php foreach (T::NICHT as $punkt): ?>
      <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn">
    <h2>So arbeiten wir.</h2>
    <ol class="zeitstrahl">
<?php foreach (T::ARBEITSWEISE as $nummer => $schritt): ?>
      <li><h3><?= (int) $nummer + 1 ?>. <?= Html::e($schritt) ?></h3></li>
<?php endforeach; ?>
    </ol>
  </div>
</section>

<section class="zusage">
  <div class="bahn">
    <p><?= Html::e(T::VERANTWORTUNG) ?></p>
  </div>
</section>

<section class="abschluss">
  <div class="bahn">
    <h2>Welche Website passt zu Ihrem Unternehmen?</h2>
    <?= Ansicht::teil('partials/handlungsblock', [
        'auftragslage' => $auftragslage,
        'preishinweis' => Websitetexte::ABSCHLUSSHINWEIS . ' ' . $preishinweis,
        'zweitziel'    => '/kontakt',
        'zweittext'    => 'Rückfrage stellen',
        'dunkel'       => true,
    ]) ?>
  </div>
</section>
