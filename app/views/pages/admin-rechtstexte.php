<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;

/** @var array<string,array<string,mixed>|null> $texte */
/** @var array<string,string> $beschriftungen */
/** @var array<string,string> $zustaende */

?>
<h1>Rechtstexte</h1>
<p>Nur freigegebene Texte gehen nach außen. Ein Entwurf bleibt hier im internen Bereich.</p>
<p>Die Freigabe setzt ein Mensch, mit Datum und Namen der prüfenden Stelle.</p>

<div class="karte">
  <ul class="liste">
<?php foreach ($beschriftungen as $slug => $beschriftung): ?>
<?php $text = $texte[$slug] ?? null; ?>
    <li>
      <span><a href="/admin/rechtstexte/<?= Html::e($slug) ?>"><?= Html::e($beschriftung) ?></a></span>
      <span>
        <span class="marke" data-stand="<?= Html::e(is_array($text) ? (string) $text['status'] : 'fehlt') ?>">
          <?= Html::e(is_array($text) ? ($zustaende[(string) $text['status']] ?? (string) $text['status']) : 'noch nicht angelegt') ?>
        </span>
      </span>
    </li>
<?php endforeach; ?>
  </ul>
</div>

<p class="leise">
  Die Entwürfe entstehen und werden anwaltlich geprüft. Bis dahin bleibt die Veröffentlichung
  gesperrt.
</p>
