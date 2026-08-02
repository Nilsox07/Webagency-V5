<?php

declare(strict_types=1);

use Sartu\Helpers\Csrf;

/**
 * Die Kopfzeile des Kundenbereichs.
 *
 * **Nur gebaute Bereiche stehen im Menue** (§0.3b: keine toten Menuepunkte, nichts
 * Ausgegrautes). Seit Stufe B sind alle acht da.
 *
 * Der Punkt heisst `Oeffnungszeiten`, nicht `Inhalte`. §8.7 ueberschreibt die Seite so, und
 * das ist auch das, was sie kann — `Inhalte` verspricht Texte und Bilder, die der Kunde
 * nicht selbst pflegt. Die Adresse bleibt `/portal/inhalte`, weil §8.7 sie so nennt.
 *
 * Die Reihenfolge ist die aus §8 und wird nicht umgestellt: Uebersicht · Angebot · Aufgaben ·
 * Vorschau · Rechnungen · Domain · Inhalte · Vertrag · Hilfe. Was fehlt, faellt heraus; was
 * bleibt, behaelt seinen Platz.
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
      <a href="/portal/aufgaben">Aufgaben</a>
      <a href="/portal/vorschau">Vorschau</a>
      <a href="/portal/rechnungen">Rechnungen</a>
      <a href="/portal/domain">Domain</a>
      <a href="/portal/inhalte">Öffnungszeiten</a>
      <a href="/portal/vertrag">Vertrag</a>
      <a href="/portal/hilfe">Hilfe</a>
      <form method="post" action="/portal/abmelden">
        <?= Csrf::feld() ?>
        <button class="knopf knopf--ruhig" type="submit">Abmelden</button>
      </form>
    </nav>
    <?php endif; ?>
  </div>
</header>
