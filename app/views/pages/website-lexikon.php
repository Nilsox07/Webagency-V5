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
<?= Ansicht::teil('partials/seitenaufmacher', [
    'vorzeile'  => 'Lexikon',
    'h1'        => (string) $eintrag['begriff'],
    'vorspann'  => (string) $eintrag['kurz'],
    'hauptziel' => $eintrag['ziel'],
    'zweitziel' => '/lexikon',
    'zweittext' => 'Alle Begriffe',
]) ?>

<section class="abschnitt">
  <div class="bahn schmal">
    <h2>Warum das für Firmenwebsites zählt</h2>
    <p><?= Html::e((string) $eintrag['warum']) ?></p>

    <h2>Ein Beispiel</h2>
    <p><?= Html::e((string) $eintrag['beispiel']) ?></p>
  </div>
</section>

<?php /* **Dunkel statt Sand, ab 14.08.2026.** Gemessen davor: sechzehn Unterseiten, null
         dunkle Abschnitte. Der Wechsel hell-dunkel-hell ist der Rhythmus des Auftritts, und
         diese Stelle ist die inhaltlich richtige — der typische Fehler und die eigene
         Antwort darauf sind der Grund, warum der Begriff eine Seite hat.

         **Der Verweis auf die Leistungsseite ist hier entfallen.** Er stand dreimal auf
         derselben Seite: als Knopf im Aufmacher, hier als Textlink und unten im
         Abschluss. Der Aufmacher trägt ihn als primäre Handlung. */ ?>
<section class="abschnitt abschnitt--dunkel">
  <div class="bahn schmal">
    <h2>Der typische Fehler</h2>
    <p><?= Html::e((string) $eintrag['fehler']) ?></p>

    <h2>Wie SARTU damit umgeht</h2>
    <p><?= Html::e((string) $eintrag['sartu']) ?></p>
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
    <div class="handlungsfeld">
      <div class="handlungsfeld__text">
        <h2>Welche Website passt zu Ihrem Unternehmen?</h2>
      </div>

      <div class="handlungsfeld__handlung">
        <?= Ansicht::teil('partials/handlungsblock', [
            'auftragslage' => $auftragslage,
            'preishinweis' => Websitetexte::ABSCHLUSSHINWEIS . ' ' . $preishinweis,
            'zweitziel'    => $eintrag['ziel'][0],
            'zweittext'    => $eintrag['ziel'][1],
            'dunkel'       => true,
        ]) ?>
      </div>
    </div>
  </div>
</section>
</article>
