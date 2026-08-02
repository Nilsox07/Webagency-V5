<?php

declare(strict_types=1);

use Sartu\Helpers\Csrf;

/**
 * Die Kopfzeile des Kundenbereichs.
 *
 * **Nur gebaute Bereiche stehen im Menue** (§0.3b: keine toten Menuepunkte, nichts
 * Ausgegrautes). `Aufgaben`, `Rechnungen`, `Vorschau` und `Domain` erscheinen, sobald A2
 * und A3 sie bauen — nicht vorher.
 *
 * @var bool $angemeldet
 */

?>
<header class="kundenband">
  <div class="bahn kundenband__reihe">
    <a class="wortmarke" href="/portal">SARTU</a>
    <?php if ($angemeldet): ?>
    <nav aria-label="Ihr Bereich">
      <a href="/portal">Übersicht</a>
      <a href="/portal/angebot">Angebot</a>
      <form method="post" action="/portal/abmelden">
        <?= Csrf::feld() ?>
        <button class="knopf knopf--ruhig" type="submit">Abmelden</button>
      </form>
    </nav>
    <?php endif; ?>
  </div>
</header>
