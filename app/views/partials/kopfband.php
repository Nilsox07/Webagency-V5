<?php

declare(strict_types=1);

use Sartu\Helpers\Csrf;

/** @var bool $angemeldet */

?>
<header class="kopfband">
  <div class="bahn kopfband__reihe">
    <a class="wortmarke" href="/admin">SARTU</a>
    <?php if ($angemeldet): ?>
    <nav aria-label="Interner Bereich">
      <a href="/admin">Übersicht</a>
      <a href="/admin/anfragen">Anfragen</a>
      <a href="/admin/projekte">Projekte</a>
      <a href="/admin/rechnungen">Rechnungen</a>
      <a href="/admin/einstellungen/betrieb">Betreiberdaten</a>
      <a href="/admin/rechtstexte">Rechtstexte</a>
      <a href="/admin/testmail">Testmail</a>
      <form method="post" action="/admin/abmelden">
        <?= Csrf::feld() ?>
        <button class="knopf knopf--ruhig" type="submit">Abmelden</button>
      </form>
    </nav>
    <?php endif; ?>
  </div>
</header>
