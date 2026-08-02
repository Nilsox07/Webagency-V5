<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Html;
use Sartu\Services\Leistungsseiten;
use Sartu\Services\Websitetexte;

/**
 * Das gemeinsame Template der fünf Leistungsseiten — Website-Lastenheft §10.
 *
 * **Die Blockreihenfolge ist verbindlich und steht deshalb nur hier:**
 * H1 → Kurz gesagt → Für wen das passt → Was enthalten ist → Was nicht enthalten ist →
 * Was es kostet → Wie es abläuft → Welche Entscheidung wir Ihnen abnehmen → FAQ → CTA.
 *
 * Fünf eigene Ansichtsdateien wären fünf Gelegenheiten, davon abzuweichen.
 *
 * **Die Pflichtsätze stehen als eigener Block**, nicht im Fließtext. Bei den Seiten 2, 3 und
 * 4 sind sie ein Haftungsschutz (§10) — im Fließtext fällt so ein Satz beim Kürzen weg.
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

    <?= Ansicht::teil('partials/handlungsblock', [
        'auftragslage' => $auftragslage,
        'preishinweis' => $preishinweis,
        'zweitziel'    => '/preise',
        'zweittext'    => 'Preise ansehen',
    ]) ?>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn schmal">
    <h2>Kurz gesagt</h2>
    <p class="lede"><?= Html::e((string) $seite['kurz']) ?></p>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn">
    <div class="zweispalten">
      <div>
        <h2>Für wen das passt.</h2>
        <ul class="hakenliste">
<?php foreach ($seite['fuer_wen'] as $punkt): ?>
          <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h2>Was Sie nicht entscheiden müssen.</h2>
        <ul class="hakenliste">
<?php foreach ($seite['abgenommen'] as $punkt): ?>
          <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="abschnitt abschnitt--sand">
  <div class="bahn">
    <div class="zweispalten">
      <div>
        <h2>Was enthalten ist.</h2>
        <ul class="hakenliste">
<?php foreach ($seite['enthalten'] as $punkt): ?>
          <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h2>Was nicht enthalten ist.</h2>
        <ul class="hakenliste">
<?php foreach ($seite['nicht_enthalten'] as $punkt): ?>
          <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
        </ul>
      </div>
    </div>

<?php foreach ($seite['pflichtsaetze'] as $satz): ?>
    <p class="hervor"><?= Html::e($satz) ?></p>
<?php endforeach; ?>
  </div>
</section>

<section class="abschnitt">
  <div class="bahn schmal">
    <h2>Was es kostet.</h2>
    <p class="lede"><?= Html::e((string) $seite['kosten']) ?></p>
    <p class="preishinweis"><?= Html::e($preishinweis) ?></p>
    <p><a class="textlink" href="/preise">Alle Preise ansehen</a></p>
  </div>
</section>

<?php if ($schluessel === 'portal'): ?>
<section class="abschnitt abschnitt--sand">
  <div class="bahn">
    <h2><?= Html::e(Websitetexte::OHNE_TERMIN) ?></h2>
    <div class="zweispalten">
<?php foreach (Leistungsseiten::kundenbereichListen() as $ueberschrift => $punkte): ?>
      <div>
        <h3><?= Html::e($ueberschrift) ?></h3>
        <ul class="hakenliste">
<?php foreach ($punkte as $punkt): ?>
          <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
        </ul>
      </div>
<?php endforeach; ?>
    </div>
    <p><?= Html::e(Websitetexte::TROTZDEM_ERREICHBAR) ?></p>
    <p><a class="textlink" href="/login">Zur Anmeldung für bestehende Kunden</a></p>
  </div>
</section>
<?php endif; ?>

<section class="abschnitt<?= $schluessel === 'portal' ? '' : ' abschnitt--sand' ?>">
  <div class="bahn">
    <h2>Wie es abläuft.</h2>
    <ol class="zeitstrahl">
<?php foreach ($seite['ablauf'] as $nummer => $schritt): ?>
      <li><h3><?= (int) $nummer + 1 ?>. <?= Html::e($schritt) ?></h3></li>
<?php endforeach; ?>
    </ol>
    <p><a class="textlink" href="/ablauf">Ablauf im Einzelnen</a></p>
  </div>
</section>

<section class="abschnitt" id="fragen">
  <div class="bahn schmal">
    <h2>Häufige Fragen</h2>
    <?= Ansicht::teil('partials/fragenliste', ['fragen' => $seite['fragen']]) ?>
  </div>
</section>

<section class="abschluss">
  <div class="bahn">
    <h2>Welche Website passt zu Ihrem Unternehmen?</h2>
    <?= Ansicht::teil('partials/handlungsblock', [
        'auftragslage' => $auftragslage,
        'preishinweis' => Websitetexte::ABSCHLUSSHINWEIS . ' ' . $preishinweis,
        'zweitziel'    => '/leistungen',
        'zweittext'    => 'Alle Leistungen',
        'dunkel'       => true,
    ]) ?>
  </div>
</section>
