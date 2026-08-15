<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/**
 * Das Ergebnis **vor** den Kontaktdaten — Website-Lastenheft §9.3.
 *
 * §9.3 verbietet hier drei Dinge ausdrücklich: **keine** Paketwechsel-Knöpfe, **keine**
 * Zusatzoptionen, **keine** SEO-Auswahl. Auf dieser Seite steht deshalb genau ein Knopf.
 *
 * Der Pflichthinweis steht unter jeder Preisnennung (§2) und wird nicht gekürzt.
 *
 * @var array<string,mixed>      $text        aus Empfehlungstext::fuer()
 * @var array<string,mixed>|null $preise      die Zeile aus der Preistabelle
 * @var string|null              $preiszeile
 * @var string|null              $erstesJahr
 */

$merkmale = [];

if ($preise !== null) {
    $merkmale[] = (string) $preise['seiten'];

    if ((int) $preise['korrekturrunden'] > 0) {
        $merkmale[] = (int) $preise['korrekturrunden'] === 1
            ? '1 Korrekturrunde'
            : $preise['korrekturrunden'] . ' Korrekturrunden';
    }

    $merkmale[] = (string) $preise['schutz'];
}

?>
<p class="vorzeile">Ihr Ergebnis</p>
<h1><?= Html::e((string) $text['ueberschrift']) ?></h1>

<?php if ($text['satz'] !== null): ?>
<p class="lead"><?= Html::e((string) $text['satz']) ?></p>
<?php endif; ?>

<?php if ($text['aufzaehlung'] !== []): ?>
<p class="lead">Das haben Sie angegeben:</p>
<ul class="punkte">
<?php foreach ($text['aufzaehlung'] as $glied): ?>
  <li><?= Html::e(ucfirst((string) $glied)) ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if ($text['folge'] !== null): ?>
<p><?= Html::e((string) $text['folge']) ?></p>
<?php endif; ?>

<?php if ($preiszeile !== null): ?>
<div class="preisblock">
  <p class="preisblock__zeile"><strong><?= Html::e($preiszeile) ?></strong></p>
  <p class="preisblock__jahr">Erstes Jahr: <strong><?= Html::e((string) $erstesJahr) ?></strong></p>
  <ul class="punkte">
<?php foreach ($merkmale as $merkmal): ?>
    <li><?= Html::e((string) $merkmal) ?></li>
<?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<p class="fussnote"><?= Html::e((string) $text['hinweis']) ?></p>

<div class="knopfreihe">
  <a class="knopf" href="/briefing/kontakt">Empfehlung unverbindlich prüfen lassen</a>
  <a class="knopf knopf--ruhig" href="/briefing/<?= Html::e((string) \Sartu\Services\Bedarfsscheck::SCHRITTE) ?>">Angaben ändern</a>
</div>

<?php /* **Der Förderhinweis — `17_SEITEN_SARTU.md` §2.3, ergänzt am 09.08.2026, gebaut am
         15.08.2026.** Er stand seit über einer Woche verbindlich in der Spezifikation und
         war nie gebaut und nie gemeldet.

         **Er ist keine Werbung, sondern Sorgfalt.** Ein Förderantrag muss vor
         Vorhabensbeginn gestellt sein. Wer hier weiterklickt, annimmt und erst danach von
         der Förderung erfährt, **verliert den Anspruch — durch unseren eigenen Ablauf.**

         Die vier Grenzen aus §2.3 sind eingehalten: keine Summe, keine Quote, kein
         Bundesland, keine Zusage. Kein Konjunktiv, der nach Versprechen klingt — „greift nur
         in bestimmten Fällen" ist die Aussage der Recherche vom 09.08.2026, nicht ihre
         Abschwächung.

         **Unterhalb des Knopfes und eine Stufe kleiner**, damit er den Pflichthinweis
         darüber nicht verdrängt: `.fussnote` ist dieselbe Stufe, die jener trägt. */ ?>
<p class="fussnote"><?= Html::e(\Sartu\Services\Foerdertexte::HINWEIS_ERGEBNIS) ?>
  <a href="<?= Html::e(\Sartu\Services\Foerdertexte::PFAD) ?>">Was zur Förderung gilt</a>.</p>
