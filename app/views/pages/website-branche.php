<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Html;
use Sartu\Services\Angebotstexte;
use Sartu\Services\Auftragslage;
use Sartu\Services\Preise;
use Sartu\Services\Startseitentexte;
use Sartu\Services\Websitetexte;

/**
 * Eine Branchenseite — Website-Lastenheft §10a, zehn Blöcke in dieser Reihenfolge.
 *
 * **Block 2 nimmt die Antwort vorweg, obwohl das Problem erst in Block 3 steht.** Das ist
 * Absicht und bedient zwei Leser: Wer überfliegt oder aus einer KI-Antwort kommt, bekommt in
 * 40–60 Wörtern Antwort und Preisanker. Wer bleibt, bekommt ab Block 3 den ganzen Bogen.
 * „Wer eine der beiden Ordnungen aufräumt, zerstört die andere."
 *
 * **Zwischen Block 2 und der ersten Aussage darüber, was SARTU liefert, stehen höchstens
 * 150 Wörter.** Der Leser liest abends auf dem Telefon.
 *
 * **Statistiken:** höchstens drei je Seite, nie im Aufmacher, Quelle und Jahr **an der
 * Zahl**. Sie stehen deshalb in Block 3 und tragen ihre Quelle im selben Satz.
 *
 * **Der Konfigurator ist eingebettet** (Block 9), nicht verlinkt — §10a: „Wer erst zu
 * `/briefing` klicken muss, klickt oft gar nicht." Es ist derselbe Endpunkt wie überall,
 * mit `branche_vorbelegt` als verstecktem Feld.
 *
 * @var array<string,mixed> $seite
 * @var string $schluessel
 * @var array<string,string>|null $auftragslage
 * @var string $preishinweis
 */

?>
<section class="aufmacher">
  <div class="bahn">
    <h1><?= Html::e((string) $seite['h1']) ?></h1>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn schmal">
    <h2>Kurz gesagt</h2>
    <p class="lede"><?= Html::e((string) $seite['kurz']) ?></p>
    <p class="preishinweis"><?= Html::e($preishinweis) ?></p>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn">
    <h2>Was <?= Html::e((string) $seite['branche']) ?> wirklich beschäftigt.</h2>

    <ul class="leistungszeilen">
<?php foreach ($seite['probleme'] as $problem): ?>
      <li>
        <h3><?= Html::e($problem['titel']) ?></h3>
        <p><?= Html::e($problem['text']) ?></p>
      </li>
<?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn schmal">
    <h2>Was auf so eine Website gehört.</h2>
    <ul class="hakenliste">
<?php foreach ($seite['gehoert_drauf'] as $punkt): ?>
      <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn">
    <h2>Was Sie in dieser Branche beachten müssen.</h2>

    <ul class="leistungszeilen">
<?php foreach ($seite['beachten'] as $punkt): ?>
      <li>
        <h3><?= Html::e($punkt['titel']) ?></h3>
        <p><?= Html::e($punkt['text']) ?></p>
      </li>
<?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn">
    <h2>Ein Beispiel</h2>
    <p class="marken">Musterprojekt — kein Kundenauftrag</p>
    <h3><?= Html::e($seite['beispiel']['titel']) ?></h3>
    <p><?= Html::e($seite['beispiel']['text']) ?></p>

    <h3>Die Seitenstruktur</h3>
    <ul class="hakenliste">
<?php foreach ($seite['beispiel']['seiten'] as $seitenname): ?>
      <li><?= Html::e($seitenname) ?></li>
<?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn">
    <h2>Was es kostet.</h2>
    <?= Ansicht::teil('partials/preisstufen', ['preishinweis' => $preishinweis]) ?>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn">
    <h2>Wie es abläuft.</h2>
    <ol class="zeitstrahl">
<?php foreach (Startseitentexte::ablauf() as $nummer => $schritt): ?>
      <li>
        <h3><?= (int) $nummer + 1 ?>. <?= Html::e($schritt['titel']) ?></h3>
        <p><?= Html::e($schritt['satz']) ?></p>
      </li>
<?php endforeach; ?>
    </ol>

    <ul class="hakenliste">
<?php foreach (['start', 'wachstum', 'platzhirsch'] as $stufe): ?>
<?php $korridor = Angebotstexte::lieferkorridor($stufe); ?>
      <li><?= Html::e(Preise::name($stufe)) ?>: <?= (int) $korridor[0] ?>–<?= (int) $korridor[1] ?> Werktage nach vollständigem Start</li>
<?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="abschluss" id="bedarfsscheck">
  <div class="bahn">
    <h2>Welche Website passt zu Ihrem Betrieb?</h2>
    <p class="lede"><?= Html::e(Startseitentexte::S10_TEXT) ?></p>

    <ul class="vertrauenszeile">
<?php foreach (Startseitentexte::S10_VERTRAUEN as $punkt): ?>
      <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
    </ul>

    <?php /* §10a: derselbe Endpunkt wie ueberall, nur mit vorbelegter Branche. */ ?>
    <form method="post" action="/briefing/start">
      <?= Csrf::feld() ?>
      <input type="hidden" name="branche_vorbelegt" value="<?= Html::e($schluessel) ?>">
      <button type="submit" class="knopf"><?= Html::e(Auftragslage::knopf($auftragslage['knopf'] ?? null)) ?></button>
    </form>

    <p class="preishinweis"><?= Html::e(Websitetexte::ABSCHLUSSHINWEIS) ?> <?= Html::e($preishinweis) ?></p>
  </div>
</section>

<section class="abschnitt" id="fragen">
  <div class="bahn schmal">
    <h2>Häufige Fragen</h2>
    <?= Ansicht::teil('partials/fragenliste', ['fragen' => $seite['fragen']]) ?>
  </div>
</section>
