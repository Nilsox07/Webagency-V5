<?php

declare(strict_types=1);

use Sartu\Helpers\Csrf;
use Sartu\Services\Startsperre;

/** @var bool $angemeldet */

/**
 * Der Punkt „Ersteinrichtung" steht hier, **solange die Startsperre greift** — und
 * verschwindet, sobald sie es nicht mehr tut. Ein Menuepunkt, der taeglich an etwas
 * Erledigtes erinnert, wird ueberlesen, und mit ihm der naechste, der etwas zu sagen hat.
 *
 * Die Seite bleibt erreichbar und ist von der Uebersicht aus verlinkt: Auf ihr steht das
 * einzige Formular fuer den Zahlungsschluessel, und der wird genau dann gewechselt, wenn
 * die Sperre laengst offen ist.
 *
 * `starterlaubt()` fragt die Datenbank. Im oeffentlichen Bereich wird dieser Teil nicht
 * gerendert, und ohne Anmeldung auch nicht — die Abfrage laeuft also je Adminseite einmal.
 */
$einrichtungOffen = false;

if ($angemeldet) {
    try {
        $einrichtungOffen = !(new Startsperre())->starterlaubt();
    } catch (\Throwable) {
        // Eine nicht lesbare Startsperre ist kein Grund, das Kopfband zu zerreissen.
        $einrichtungOffen = false;
    }
}

?>
<header class="kopfband">
  <div class="bahn kopfband__reihe">
    <a class="wortmarke" href="/admin">SARTU</a>
    <?php if ($angemeldet): ?>
    <nav aria-label="Interner Bereich">
      <a href="/admin">Übersicht</a>
<?php if ($einrichtungOffen): ?>
      <a href="/admin/ersteinrichtung">Ersteinrichtung</a>
<?php endif; ?>
      <a href="/admin/anfragen">Anfragen</a>
      <a href="/admin/projekte">Projekte</a>
      <a href="/admin/rechnungen">Rechnungen</a>
      <a href="/admin/belege">Belege</a>
      <a href="/admin/nachrichten">Nachrichten</a>
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
