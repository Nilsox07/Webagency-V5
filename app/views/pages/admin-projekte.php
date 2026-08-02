<?php

declare(strict_types=1);

use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Preise;
use Sartu\Services\Projektstatus;

/**
 * Die Projektliste im internen Bereich.
 *
 * @var list<array<string,mixed>> $projekte
 * @var array<string,string> $organisationen
 */

?>
<h1>Projekte</h1>

<?php if ($projekte === []): ?>
<div class="karte">
  <p>Sobald Sie eine Anfrage in Kunde und Projekt umwandeln, steht das Projekt hier.</p>
</div>
<?php else: ?>
<div class="karte">
  <div class="tabellenrahmen">
    <table class="tabelle">
      <thead>
        <tr>
          <th scope="col">Angelegt</th>
          <th scope="col">Projekt</th>
          <th scope="col">Kunde</th>
          <th scope="col">Umfang</th>
          <th scope="col">Stand</th>
        </tr>
      </thead>
      <tbody>
<?php foreach ($projekte as $projekt): ?>
        <tr>
          <td><a href="/admin/projekte/<?= Html::e((string) $projekt['id']) ?>"><?= Html::e(Format::datum((string) $projekt['created_at'])) ?></a></td>
          <td><?= Html::e(Format::text((string) $projekt['title'])) ?></td>
          <td><?= Html::e($organisationen[(string) $projekt['organization_id']] ?? Format::LEER) ?></td>
          <td><?= Html::e(Preise::name((string) $projekt['package'])) ?></td>
          <td><?= Html::e(Projektstatus::kundentext((string) $projekt['status'])) ?></td>
        </tr>
<?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
