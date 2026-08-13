<?php

declare(strict_types=1);

use Sartu\Helpers\Csrf;
use Sartu\Helpers\Html;
use Sartu\Services\Startsperre;

/**
 * Die Seitenleiste des internen Bereichs — `design/portalkonzept.html`, abgenommen 03.08.2026.
 *
 * ## Derselbe Umbau wie im Kundenbereich, am 10.08.2026
 *
 * Bis dahin ein waagerechtes Kopfband; der Vermerk in `design/PORTAL_DESIGNSTAND.md` fuehrte
 * auch diese Datei als „beim Uebertragen ersetzen".
 *
 * ## Woran sich die beiden Bereiche unterscheiden
 *
 * `CODEX_AUFTRAG_PORTAL.md` §4 verlangt, dass sie **visuell unterscheidbar** sind. Der
 * Unterschied liegt nicht in einer zweiten Farbwelt, sondern in Flaeche und Dichte:
 *
 * | | Kundenbereich | Interner Bereich |
 * |---|---|---|
 * | Leiste | `--ink` | `--ink-2`, eine Spur heller |
 * | Zusatz an der Marke | `Ihr Bereich` | `Intern` |
 * | Grundschrift | 17 px | 15,5 px |
 * | Gruppen | Projekt · Verwaltung · Kontakt | Arbeit · Betrieb · Werkzeug |
 *
 * Die beiden Toene stehen dafuer seit dem 03.08.2026 in `tokens.css` — und wurden bis zum
 * 10.08.2026 **von keiner Regel benutzt**, weil es die Leiste nicht gab.
 *
 * ## Der Punkt, der verschwindet
 *
 * `Ersteinrichtung` steht nur, solange `Startsperre::starterlaubt()` falsch ist. Danach hat er
 * seine Aufgabe erfuellt; die Seite bleibt ueber die Uebersicht erreichbar. Ein Menuepunkt,
 * der taeglich an etwas Erledigtes erinnert, wird ueberlesen — und mit ihm der naechste.
 *
 * @var bool $angemeldet
 * @var string $pfad
 */

$pfad ??= '';

$einrichtungOffen = false;

if ($angemeldet) {
    try {
        $einrichtungOffen = !(new Startsperre())->starterlaubt();
    } catch (\Throwable) {
        // Eine nicht lesbare Startsperre ist kein Grund, die Leiste zu zerreissen.
        $einrichtungOffen = false;
    }
}

$eintrag = static function (string $ziel, string $zeichen, string $wort) use ($pfad): string {
    $aktiv = $ziel === '/admin'
        ? $pfad === '/admin'
        : ($pfad === $ziel || str_starts_with($pfad, $ziel . '/'));

    return '<a href="' . Html::e($ziel) . '"' . ($aktiv ? ' class="an" aria-current="page"' : '') . '>'
        . '<svg class="ik" aria-hidden="true" focusable="false"><use href="#' . Html::e($zeichen) . '"/></svg>'
        . Html::e($wort)
        . '</a>';
};

?>
<nav class="rail rail--intern" aria-label="Interner Bereich">
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
           Intern waere „Adminbereich" zulaessig; er heisst hier so, weil beide Leisten
           dieselbe Form tragen und nebeneinander gezeigt werden. */ ?>
  <a class="rail-marke" href="/admin">
    <img src="/assets/bild/sartu-logo-dunkel.svg" alt="SARTU" width="160" height="30">
    <em>Adminbereich</em>
  </a>

<?php if ($angemeldet): ?>
  <div class="rail-gruppe">
    <span class="mono">Arbeit</span>
    <?= $eintrag('/admin', 'i-uebersicht', 'Übersicht') ?>
    <?= $eintrag('/admin/anfragen', 'i-anfragen', 'Anfragen') ?>
    <?= $eintrag('/admin/projekte', 'i-projekte', 'Projekte') ?>
    <?= $eintrag('/admin/nachrichten', 'i-hilfe', 'Nachrichten') ?>
  </div>

  <div class="rail-gruppe">
    <span class="mono">Betrieb</span>
    <?= $eintrag('/admin/rechnungen', 'i-rechnungen', 'Rechnungen') ?>
    <?= $eintrag('/admin/belege', 'i-export', 'Belege') ?>
    <?= $eintrag('/admin/einstellungen/betrieb', 'i-betreiberdaten', 'Betreiberdaten') ?>
    <?= $eintrag('/admin/rechtstexte', 'i-rechtstexte', 'Rechtstexte') ?>
  </div>

  <div class="rail-gruppe">
    <span class="mono">Werkzeug</span>
<?php if ($einrichtungOffen): ?>
    <?= $eintrag('/admin/ersteinrichtung', 'i-stand', 'Ersteinrichtung') ?>
<?php endif; ?>
    <?= $eintrag('/admin/testmail', 'i-testmail', 'Testmail') ?>
  </div>

  <div class="rail-fuss">
    <form method="post" action="/admin/abmelden">
      <?= Csrf::feld() ?>
      <button type="submit" class="rail-abmelden">
        <svg class="ik" aria-hidden="true" focusable="false"><use href="#i-abmelden"/></svg>
        Abmelden
      </button>
    </form>
  </div>
<?php endif; ?>
</nav>
