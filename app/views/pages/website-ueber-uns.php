<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Html;
use Sartu\Services\Firmenseitentexte as T;
use Sartu\Services\Websitetexte;

/**
 * `/ueber-uns` — Website-Lastenheft §11.
 *
 * **Der Gründerabschnitt hängt an den Daten, nicht am Code** — `SARTU_ENTSCHEIDUNGEN_OFFEN.md`
 * §4c, 10.08.2026. Stehen Name, Text und Bild in den Betreiberdaten, steht er hier; fehlt
 * eines davon, entfällt er. Ein Platzhalter, der wie ein Foto wirkt, bleibt unzulässig.
 *
 * @var array<string,string>|null $auftragslage
 * @var array{name:string,text:string,bild:string}|null $gruender
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

<?php /* §11 verlangt hier den Abschnitt mit echtem Foto. Er steht, sobald Name, Text und
         Bild in den Betreiberdaten stehen — sonst entfaellt er (§4c).
         Ohne die vier Abgrenzungen und ohne den Textlink: beide stehen auf dieser Seite
         schon, und ein Link auf die Seite, die man gerade liest, ist keiner. */ ?>
<?= Ansicht::teil('partials/gruender', [
    'gruender'      => $gruender,
    'mitAbgrenzung' => false,
    'mitLink'       => false,
]) ?>

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
