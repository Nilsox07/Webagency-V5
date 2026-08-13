<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Router;

/**
 * Startsperre, Bedingung 4 und 4a — `10_WEBSITE_SARTU.md` §5, „Die Startsperre".
 *
 * ## Warum diese Klasse am 13.08.2026 entstanden ist
 *
 * `partials/bildplatz.php` behauptete seit dem Bau: *„Die Startsperre §14a Bedingung 4 sucht
 * genau diese Markierung und bricht die produktive Veröffentlichung ab."* **Das stimmte
 * nicht.** `Startsperre` prüft ausschließlich den Zustand der Einstellungen — ihr eigener
 * Klassenkopf sagt das sogar: „Sie prueft nicht auf Platzhalter in Vorlagen."
 *
 * Damit war jeder Bildplatz eine Zusage ohne Deckung: Die Markierung stand im Markup, niemand
 * suchte sie, und der Text daneben versicherte, dass jemand es täte. Aufgefallen ist es, als
 * Sektion 6 und 8 gebaut wurden — beide leben von dieser Zusage.
 *
 * ## Warum sie nicht in `Startsperre::hindernisse()` steht
 *
 * `hindernisse()` läuft bei **jedem** Aufruf einer Adminseite: `partials/kopfband.php` fragt
 * damit, ob der Einrichtungshinweis erscheint. Diese Prüfung hier rendert dagegen ein Dutzend
 * öffentliche Seiten. In den Kopf jeder Adminseite gehört sie deshalb nicht.
 *
 * Sie hängt statt dessen an `bin/startklar.php` — dem Befehl, der die Veröffentlichung
 * freigibt oder mit **1** abbricht. Das ist die Stelle, an der §5 sie verlangt: „Der
 * Veröffentlichungsvorgang für Produktion bricht mit Fehler ab."
 *
 * ## Geprüft wird das **ausgelieferte** Markup, nicht die Vorlage
 *
 * Eine Suche über `app/views` fände `bildplatz.php` selbst und meldete den Fehler für immer,
 * auch wenn keine Seite den Bildplatz mehr einbindet. Gerendert wird deshalb wirklich — über
 * denselben Router, der auch den Besucher bedient.
 */
final class Platzhalterpruefung
{
    /**
     * Die drei einheitlichen Markierungen aus §5.
     *
     * „Alle Platzhalter tragen **eine** einheitliche, suchbare Markierung. **Keine freien
     * Formulierungen wie ‚TODO' oder ‚Lorem ipsum'** — die findet niemand wieder."
     */
    public const MARKIERUNGEN = ['[[PLATZHALTER]]', '[[SCREENSHOT-FEHLT]]', '[[FOTO-FEHLT]]'];

    /**
     * Zusätzlich gesperrter Wortlaut — §5 Bedingung 4a nennt ihn ausdrücklich neben den
     * Markierungen, weil er ohne Klammern in keiner Suche auftaucht.
     */
    public const GESPERRTER_WORTLAUT = ['Name wird nachgereicht'];

    /** @var list<string>|null */
    private ?array $adressen;

    private ?Router $router;

    /**
     * @param list<string>|null $adressen  `null` heißt: alle Launch-Adressen
     * @param Router|null $router          `null` heißt: einer mit **gesetzter**
     *                                     Installationssperre — siehe `router()`
     */
    public function __construct(?array $adressen = null, ?Router $router = null)
    {
        $this->adressen = $adressen;
        $this->router = $router;
    }

    /**
     * Ein Eintrag je Fund, mit Adresse und Markierung.
     *
     * §5: „**Die Fehlermeldung nennt die Ursache**, nicht nur den Abbruch — welche Datei,
     * welche Bedingung." Ein blosses `true`/`false` wäre an dieser Stelle wertlos: Der
     * Betreiber sieht dann, dass etwas fehlt, aber nicht wo.
     *
     * @return list<string>
     */
    public function hindernisse(): array
    {
        $hindernisse = [];

        foreach ($this->adressen ?? array_keys(Launchadressen::alle()) as $pfad) {
            $rumpf = $this->rumpf($pfad);

            if ($rumpf === null) {
                continue;
            }

            foreach ([...self::MARKIERUNGEN, ...self::GESPERRTER_WORTLAUT] as $marke) {
                if (!str_contains($rumpf, $marke)) {
                    continue;
                }

                $hindernisse[] = sprintf(
                    '%s liefert den Platzhalter „%s" aus. Solange er dort steht, geht die '
                    . 'Seite nicht produktiv.',
                    $pfad,
                    $marke
                );
            }
        }

        return $hindernisse;
    }

    /**
     * Das ausgelieferte Markup einer Adresse — oder `null`, wenn sie nicht antwortet.
     *
     * **Ein Fehler beim Rendern ist hier kein Hindernis.** Eine Seite, die gar nicht
     * ausliefert, ist ein anderes Problem als eine, die einen Platzhalter ausliefert; sie hier
     * zu melden hiesse, zwei Ursachen unter einer Meldung zu verstecken.
     */
    private function rumpf(string $pfad): ?string
    {
        try {
            $antwort = $this->router()->behandeln('GET', $pfad);

            return $antwort->status === 200 ? (string) $antwort->rumpf : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Der Router, durch den gerendert wird — **mit gesetzter Installationssperre.**
     *
     * ## Warum die Sperre hier gesetzt sein muss
     *
     * Ist die Ersteinrichtung offen, leitet der Router **jede** öffentliche Adresse auf
     * `/admin/setup` um (§1.5). Die Antwort wäre dann kein `200`, diese Prüfung liesse jede
     * Seite aus und meldete „kein Hindernis" — ausgerechnet an dem Punkt, an dem noch gar
     * nichts fertig ist.
     *
     * Am Veröffentlichungsgatter ist die Einrichtung abgeschlossen; dort träte der Fall nie
     * auf. Er tritt im Test auf, und wäre er dort unbemerkt geblieben, hätte die Prüfung
     * genau die Zusicherung wiederholt, für deren Fehlen sie gebaut wurde.
     *
     * Deshalb eine Sperre auf einem Verzeichnis mit Sperrdatei: `gesperrt()` ist dann `true`,
     * ohne dass diese Klasse eine Datenbankspalte anfasst.
     */
    private function router(): Router
    {
        if ($this->router !== null) {
            return $this->router;
        }

        $verzeichnis = sys_get_temp_dir() . '/sartu-platzhalterpruefung';

        if (!is_dir($verzeichnis)) {
            mkdir($verzeichnis, 0770, true);
        }

        touch($verzeichnis . '/' . InstallationsSperre::DATEINAME);

        return $this->router = new Router(
            require dirname(__DIR__) . '/routes.php',
            new InstallationsSperre(null, $verzeichnis),
        );
    }
}
