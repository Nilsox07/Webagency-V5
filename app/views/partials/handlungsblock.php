<?php

declare(strict_types=1);

use Sartu\Helpers\Html;
use Sartu\Services\Auftragslage;

/**
 * Knopf, Statuszeile und Preishinweis — Website-Lastenheft §5a und §5 Sektion 1 / 10.
 *
 * **Die Statuszeile erscheint an genau zwei Stellen**: in der Aufmacher-Karte beim
 * Hauptknopf und in Sektion 10 beim Abschluss. §5a: „Sonst nirgends — insbesondere kein
 * Vollbreiten-Streifen über der Navigation."
 *
 * **Bei `ausgebucht` steht sie über dem Knopf**, sonst darunter, und die Beschriftung
 * wechselt auf `Auf die Warteliste`. Das ist keine Gestaltungsfrage: Eine Anfrage wäre dann
 * eine Sackgasse.
 *
 * **Ist nichts gesetzt, steht hier nichts.** Kein „Freie Kapazitäten" als Vorgabe — das wäre
 * eine Aussage über den Betrieb, die niemand getroffen hat.
 *
 * @var array<string,string>|null $auftragslage
 * @var string $preishinweis
 * @var string|null $zweitziel
 * @var string|null $zweittext
 * @var bool $dunkel
 */

$oben = ($auftragslage['gewicht'] ?? null) === 'betont';
$knopf = $auftragslage['knopf'] ?? Auftragslage::KNOPF;

$zeile = $auftragslage === null ? '' : '<p class="lage lage--' . Html::e($auftragslage['gewicht'])
    . '"><span class="lage__punkt lage__punkt--' . Html::e($auftragslage['fuellung'])
    . '" aria-hidden="true"></span>' . Html::e($auftragslage['text']) . '</p>';

?>
<div class="handlung<?= ($dunkel ?? false) ? ' handlung--dunkel' : '' ?>">
<?php if ($oben): ?>
  <?= $zeile ?>
<?php endif; ?>

  <p class="handlung__knoepfe">
    <a class="knopf" href="/briefing"><?= Html::e($knopf) ?></a>
<?php if (($zweitziel ?? null) !== null): ?>
    <a class="textlink" href="<?= Html::e($zweitziel) ?>"><?= Html::e((string) $zweittext) ?></a>
<?php endif; ?>
  </p>

<?php if (!$oben): ?>
  <?= $zeile ?>
<?php endif; ?>

  <p class="preishinweis"><?= Html::e($preishinweis) ?></p>
</div>
