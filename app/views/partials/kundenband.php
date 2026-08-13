<?php

declare(strict_types=1);

use Sartu\Helpers\Csrf;
use Sartu\Helpers\Html;

/**
 * Die Seitenleiste des Kundenbereichs — `design/portalkonzept.html`, abgenommen 03.08.2026.
 *
 * ## Was sich am 10.08.2026 geaendert hat
 *
 * Bis dahin stand hier ein **waagerechtes Kopfband**. Der Betreiber hatte am 03.08.2026 die
 * Seitenleiste entschieden; der Vermerk in `design/PORTAL_DESIGNSTAND.md` fuehrte diese Datei
 * ausdruecklich als „beim Uebertragen ersetzen". Sie ist ersetzt.
 *
 * Die zweite Sperre lag an Punkt 9 in `OFFENE_ENTSCHEIDUNGEN.md` — ob es Zeichen geben darf.
 * Am 10.08.2026 entschieden: **ja**. Deshalb steht vor jedem Eintrag eines.
 *
 * ## Die Reihenfolge ist gebunden, der Wortlaut nicht mehr
 *
 * §8 gibt die neun Punkte und ihre Reihenfolge vor: Uebersicht · Angebot · Aufgaben ·
 * Vorschau · Rechnungen · Domain · Inhalte · Vertrag · Hilfe. **Daran wird nicht gedreht.**
 *
 * Der Wortlaut ist seit dem 09.08.2026 Klasse 2 (`sartu-texter`): frei formulierbar, aber
 * **innerhalb einer Fassung identisch** und als Liste im Pruefbericht. Uebernommen sind
 * deshalb die Woerter aus §8 — sie sind kurz, eindeutig und stehen bereits ueberall so.
 *
 * **Der siebte Punkt heisst `Inhalte`, nicht `Oeffnungszeiten`.** Entscheidung des Betreibers
 * vom 03.08.2026: Das Menuewort ist `Inhalte` (§8), die Ueberschrift der Seite bleibt
 * `Oeffnungszeiten` (§8.7). Das ist kein Widerspruch, sondern Arbeitsteilung zwischen
 * Navigation und Seitentitel — bis zum 10.08.2026 stand hier das Wort der Ueberschrift.
 *
 * ## Drei Gruppen, und warum die Zahl nur manchmal dasteht
 *
 * Projekt · Verwaltung · Kontakt. Ein Zaehler erscheint **nur, wo etwas offen ist** — eine
 * dauerhafte `0` neben `Aufgaben` waere eine Zahl ohne Aussage, und der Blick lernt sie zu
 * uebersehen. Genau dann faellt die `3` daneben auch nicht mehr auf.
 *
 * @var bool $angemeldet
 * @var string $pfad          der aufgerufene Pfad, fuer die Markierung des aktiven Eintrags
 * @var array<string,int> $zaehler  offene Posten je Punkt, nur gesetzte Schluessel erscheinen
 */

$pfad ??= '';
$zaehler ??= [];

/**
 * Ein Eintrag der Leiste.
 *
 * Aktiv ist er, wenn der Pfad genau passt oder unterhalb liegt — `/portal/aufgaben/17`
 * markiert `Aufgaben`. `/portal` waere sonst bei jedem Unterpfad mit aktiv, deshalb dort
 * der genaue Vergleich.
 */
$eintrag = static function (string $ziel, string $zeichen, string $wort) use ($pfad, $zaehler): string {
    $aktiv = $ziel === '/portal'
        ? $pfad === '/portal'
        : ($pfad === $ziel || str_starts_with($pfad, $ziel . '/'));

    $offen = $zaehler[$ziel] ?? 0;

    return '<a href="' . Html::e($ziel) . '"' . ($aktiv ? ' class="an" aria-current="page"' : '') . '>'
        . '<svg class="ik" aria-hidden="true" focusable="false"><use href="#' . Html::e($zeichen) . '"/></svg>'
        . Html::e($wort)
        . ($offen > 0 ? '<span class="zahl">' . (int) $offen . '</span>' : '')
        . '</a>';
};

?>
<nav class="rail" aria-label="Ihr Bereich">
  <?php /* Das **Logo** statt der getippten Wortmarke — Betreiberentscheidung vom
           13.08.2026. Bis dahin stand hier `SARTU` als Text; die Logodateien lagen
           ausgeliefert unter `/assets/bild/` und wurden von keiner Ansicht der Leiste
           eingebunden.

           Die **dunkle** Fassung: Sie traegt die Wortmarke in Weiss und gehoert auf
           dunklen Grund. `alt` bleibt der Markenname, nicht „Logo" — Bildbeschreibungen
           werden vorgelesen.

           Darunter der Bereichsname. **Nicht „Kundenportal".** `10_WEBSITE_SARTU.md` §2
           und `CLAUDE.md`: „`Portal` ist gestrichen, ersetzt durch `Kundenbereich`", und
           „nach aussen nie: App, Software, SaaS, Plattform, Tool, Dashboard, System".
           Die Leiste sieht der Kunde — und sie steht als Aufnahme auf der Website. */ ?>
  <a class="rail-marke" href="/portal">
    <img src="/assets/bild/sartu-logo-dunkel.svg" alt="SARTU" width="160" height="30">
    <em>Kundenbereich</em>
  </a>

<?php if ($angemeldet): ?>
  <div class="rail-gruppe">
    <span class="mono">Projekt</span>
    <?= $eintrag('/portal', 'i-uebersicht', 'Übersicht') ?>
    <?= $eintrag('/portal/angebot', 'i-angebot', 'Angebot') ?>
    <?= $eintrag('/portal/aufgaben', 'i-aufgaben', 'Aufgaben') ?>
    <?= $eintrag('/portal/vorschau', 'i-vorschau', 'Vorschau') ?>
  </div>

  <div class="rail-gruppe">
    <span class="mono">Verwaltung</span>
    <?= $eintrag('/portal/rechnungen', 'i-rechnungen', 'Rechnungen') ?>
    <?= $eintrag('/portal/domain', 'i-domain', 'Domain') ?>
    <?= $eintrag('/portal/inhalte', 'i-inhalte', 'Inhalte') ?>
    <?= $eintrag('/portal/vertrag', 'i-vertrag', 'Vertrag') ?>
  </div>

  <div class="rail-gruppe">
    <span class="mono">Kontakt</span>
    <?= $eintrag('/portal/hilfe', 'i-hilfe', 'Hilfe') ?>
  </div>

  <div class="rail-fuss">
    <form method="post" action="/portal/abmelden">
      <?= Csrf::feld() ?>
      <button type="submit" class="rail-abmelden">
        <svg class="ik" aria-hidden="true" focusable="false"><use href="#i-abmelden"/></svg>
        Abmelden
      </button>
    </form>
  </div>
<?php endif; ?>
</nav>
