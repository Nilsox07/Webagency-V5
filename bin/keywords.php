#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Erzeugt `KEYWORD_VALIDATION.md` — Website-Lastenheft §17, Keywordstrategie §1.1.
 *
 * ## Warum ein Skript und keine getippte Datei
 *
 * §1.1 verlangt „je Launch-Adresse eine Zeile". Es sind über dreissig, und Titel, H1 und
 * Beschreibung stehen bereits im Bau. Von Hand abgetippt wären sie am Tag nach der ersten
 * Textänderung falsch — und niemand merkte es, weil eine Prüfdatei niemand prüft.
 *
 * Deshalb wird gemessen, nicht abgeschrieben: Das Skript fährt **jede** Adresse durch den
 * echten Router und liest aus der Antwort, was der Browser bekommt.
 *
 * ## Was das Skript NICHT tut
 *
 * §1.1: „Volumen — **nur wenn ein echtes Werkzeug vorliegt.** Sonst bleibt das Feld leer —
 * **nie geschätzt**." Es liegt keines vor. Volumen, SERP-Typen und verwandte Fragen bleiben
 * deshalb leer; die Datei trägt oben die von §1.1 dafür vorgesehene Kennzeichnung.
 *
 * Die **Entscheidung** je Zeile trifft ein Mensch (§1.1: „sie bestimmt Titel und Adressen,
 * und Adressen ändert man später nur mit Weiterleitungen"). Die Spalte bleibt leer.
 *
 * Der **Zielbegriff** ist ein mechanischer Vorschlag: der Titel bis zum Trennzeichen. Das
 * ist keine Recherche und gibt sich auch nicht als eine aus — die Regel steht in der Datei.
 *
 * Aufruf: `docker compose exec app php bin/keywords.php`
 */

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Router;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Launchadressen;
use Sartu\Services\Wartungsmodus;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$wurzel = require dirname(__DIR__) . '/app/bootstrap.php';
restore_exception_handler();

$ziel = $wurzel . '/KEYWORD_VALIDATION.md';

$router = new Router(
    require $wurzel . '/app/routes.php',
    new InstallationsSperre(new BetreiberdatenSpeicher()),
    new Wartungsmodus($wurzel . '/storage/wartung.flag'),
);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] ??= 'localhost';
$_SERVER['REMOTE_ADDR'] ??= '127.0.0.1';

// Ein leerer Sitzungsspeicher, keine PHP-Sitzung: `/briefing` traegt ein CSRF-Feld, und
// `Csrf::token()` verlangt dafuer `$_SESSION` — mehr nicht. Eine echte `session_start()`
// legte auf dem Server eine Datei an, fuer einen Lauf, der nur liest.
$_SESSION = [];

/** Der erste Treffer einer Gruppe — oder `null`, wenn das Muster nicht greift. */
$auslesen = static function (string $html, string $muster): ?string {
    if (preg_match($muster, $html, $treffer) !== 1) {
        return null;
    }

    return trim(html_entity_decode($treffer[1], ENT_QUOTES, 'UTF-8'));
};

/** Was eine Tabellenzelle nicht zerreissen darf. */
$zelle = static function (?string $wert): string {
    return $wert === null || $wert === ''
        ? '**fehlt**'
        : str_replace(['|', "\n"], ['\\|', ' '], $wert);
};

$zeilen = [];
$fehlend = 0;
$gesperrt = 0;

foreach (array_keys(Launchadressen::alle()) as $pfad) {
    $antwort = $router->behandeln('GET', $pfad);

    if ($antwort->status !== 200) {
        // §14a: `/impressum` und `/datenschutz` liefern 404, solange der Rechtstext auf
        // `entwurf` steht. Das steht so in der Datei und wird nicht stillschweigend
        // uebersprungen — eine fehlende Zeile waere eine unbestaetigte Adresse.
        $zeilen[] = sprintf(
            '| `%s` | — | — | — | — | **Antwort %d, nicht bestätigbar** |',
            $pfad,
            $antwort->status,
        );
        ++$gesperrt;
        continue;
    }

    $html = $antwort->rumpf;
    $titel = $auslesen($html, '#<title>(.*?)</title>#s');
    $h1 = $auslesen($html, '#<h1[^>]*>(.*?)</h1>#s');
    $beschreibung = $auslesen($html, '#<meta name="description" content="([^"]*)"#');

    // Der Zielbegriff-Vorschlag: der Titel bis zum ersten Trennzeichen. Mechanisch.
    $vorschlag = $titel === null ? null : trim((string) preg_split('/\s+[|—–:]\s+/u', $titel)[0]);

    $zeilen[] = sprintf(
        '| `%s` | %s | %s | %s | %s | |',
        $pfad,
        $zelle($titel),
        $zelle($h1),
        $zelle($beschreibung),
        $zelle($vorschlag),
    );

    if ($titel === null || $h1 === null || $beschreibung === null) {
        ++$fehlend;
    }
}

$kopf = <<<'MARKDOWN'
# KEYWORD_VALIDATION

**SERP- und Intent-Validierung ohne Volumendaten.** Diese Kennzeichnung verlangt
`SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §1.1 für den Fall, dass kein Volumenwerkzeug vorliegt.
Es liegt keines vor. Volumen, SERP-Typen, Dominanz und verwandte Fragen bleiben deshalb
leer — §1.1: „**nie geschätzt**".

**Erzeugt, nicht getippt.** `php bin/keywords.php` fährt jede Launch-Adresse aus
`Launchadressen::alle()` durch den echten Router und liest Titel, H1 und Beschreibung aus
der Antwort. Ändert sich ein Text, wird die Datei neu erzeugt — nicht nachgepflegt.

**Der Zielbegriff ist ein Vorschlag**, gebildet aus dem Titel bis zum ersten Trennzeichen
(`|`, `—`, `:`). Das ist eine Zeichenkettenregel und keine Recherche.

**Die Spalte „Bestätigt" füllt ein Mensch.** §1.1: „Die Entscheidung je Zeile trifft ein
Mensch — sie bestimmt Titel und Adressen, und Adressen ändert man später nur mit
Weiterleitungen." Solange eine Zeile dort leer ist, ist ihre Adresse **nicht** bestätigt,
und §17 („Vor dem Livegang zwingend") ist für sie nicht erfüllt.

Einzutragen ist entweder `bestätigt` oder ein Änderungsvorschlag mit Begründung.

MARKDOWN;

$tabelle = "\n## Je Launch-Adresse eine Zeile\n\n"
    . "| URL | Titel | H1 | Beschreibung | Zielbegriff (Vorschlag) | Bestätigt |\n"
    . "|---|---|---|---|---|---|\n"
    . implode("\n", $zeilen) . "\n";

$fuss = sprintf(
    "\n## Stand\n\n%d Adressen geprüft, %d davon mit fehlender Angabe, %d ohne Antwort 200.\n"
    . "\nWas §1.1 zusätzlich verlangt und hier fehlt: Nebenbegriffe, Suchintention, "
    . "SERP-Typen der ersten zehn, Dominanz, verwandte Fragen, Volumen. Sie brauchen "
    . "Suchergebnisse und ein Volumenwerkzeug; beides steht dieser Umgebung nicht zur "
    . "Verfügung. **Nichts davon wurde geschätzt.**\n",
    count($zeilen),
    $fehlend,
    $gesperrt,
);

file_put_contents($ziel, $kopf . $tabelle . $fuss);

fwrite(STDOUT, sprintf(
    'KEYWORD_VALIDATION.md geschrieben: %d Adressen, %d mit fehlender Angabe, %d ohne Antwort 200.%s',
    count($zeilen),
    $fehlend,
    $gesperrt,
    PHP_EOL,
));

// Ein Rueckgabewert ungleich 0 nur bei einer fehlenden Angabe. Die 404 von `/impressum`
// und `/datenschutz` ist §14a und kein Fehler dieses Laufs — sie steht in der Datei.
exit($fehlend > 0 ? 1 : 0);
