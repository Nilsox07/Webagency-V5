<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Html;
use Sartu\Services\Websitetexte;

/**
 * Eine Begriffsseite — Website-Lastenheft §13, acht Teile in dieser Reihenfolge.
 *
 * H1 = Begriff · Kurzdefinition · Warum es wichtig ist · Beispiel · Typischer Fehler · Wie
 * SARTU damit umgeht · Verwandte Begriffe · Link zur Leistungsseite.
 *
 * **„Typischer Fehler" ist der Teil, der die Seite trägt.** Ein Lexikoneintrag, der nur
 * definiert, steht schon hundertmal im Netz. Der Fehler ist das, was ein Missverständnis
 * kostet — und §13 nimmt genau deshalb nur Begriffe auf, „bei denen ein Missverständnis Geld
 * kostet".
 *
 * @var array<string,mixed> $eintrag
 * @var array<string,string> $verwandte  Begriff => Adresse
 * @var array<string,string>|null $auftragslage
 * @var string $preishinweis
 */

?>
<article>
<section class="aufmacher">
  <div class="bahn schmal">
    <h1><?= Html::e((string) $eintrag['begriff']) ?></h1>
    <p class="lede"><?= Html::e((string) $eintrag['kurz']) ?></p>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn schmal">
    <h2>Warum das für Firmenwebsites zählt</h2>
    <p><?= Html::e((string) $eintrag['warum']) ?></p>

    <h2>Ein Beispiel</h2>
    <p><?= Html::e((string) $eintrag['beispiel']) ?></p>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn schmal">
    <h2>Der typische Fehler</h2>
    <p><?= Html::e((string) $eintrag['fehler']) ?></p>

    <h2>Wie SARTU damit umgeht</h2>
    <p><?= Html::e((string) $eintrag['sartu']) ?></p>
    <p><a class="textlink" href="<?= Html::e($eintrag['ziel'][0]) ?>"><?= Html::e($eintrag['ziel'][1]) ?> ansehen</a></p>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn schmal">
    <h2>Verwandte Begriffe</h2>
    <ul class="hakenliste">
<?php foreach ($verwandte as $begriff => $adresse): ?>
      <li><a href="<?= Html::e($adresse) ?>"><?= Html::e($begriff) ?></a></li>
<?php endforeach; ?>
    </ul>
    <p><a class="textlink" href="/lexikon">Alle Begriffe</a></p>
  </div>
</section>

<section class="abschluss">
  <div class="bahn">
    <h2>Welche Website passt zu Ihrem Unternehmen?</h2>
    <?= Ansicht::teil('partials/handlungsblock', [
        'auftragslage' => $auftragslage,
        'preishinweis' => Websitetexte::ABSCHLUSSHINWEIS . ' ' . $preishinweis,
        'zweitziel'    => $eintrag['ziel'][0],
        'zweittext'    => $eintrag['ziel'][1],
        'dunkel'       => true,
    ]) ?>
  </div>
</section>
</article>
