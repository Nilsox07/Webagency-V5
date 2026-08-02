<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\Uuid;

/**
 * Der Zwischenstand des Bedarfsschecks — Website-Lastenheft §9.5a.
 *
 * > „Zwischenstand serverseitig in einer kurzlebigen Sitzung (**Ablauf 24 Stunden**, nur
 * > Formulardaten, keine Kennung im Klartext in der URL)."
 *
 * Drei Festlegungen, die zusammengehören:
 *
 * | Punkt | Umsetzung |
 * |---|---|
 * | serverseitig | die Werte stehen in der PHP-Sitzung, nie in einem versteckten Feld |
 * | keine Kennung in der URL | die Schritte heißen `/briefing/1` … `/briefing/5` — eine Nummer, keine Kennung |
 * | Ablauf 24 Stunden | `begonnen_am` wird beim Start gesetzt und bei jedem Zugriff geprüft |
 *
 * **Die `submission_id` entsteht beim Start** und bleibt über alle Schritte gleich (§9.5b).
 * Daran hängt die Doppelklicksperre: Ein zweites Absenden trägt dieselbe Kennung, und
 * `AnfrageService` erkennt sie wieder. Würde sie erst beim Absenden entstehen, erzeugte
 * jeder Neuladeversuch einen zweiten Datensatz.
 *
 * **`form_started_at` ist ebenfalls der Startzeitpunkt**, nicht der des letzten Schritts.
 * Die Zeitregel aus §4b.3 misst die Dauer des ganzen Durchlaufs.
 */
final class BedarfsscheckSitzung
{
    private const SCHLUESSEL = '_bedarfsscheck';

    /** Sekunden. §9.5a nennt 24 Stunden. */
    private const ABLAUF = Bedarfsscheck::ZWISCHENSTAND_STUNDEN * 3600;

    /** Startet einen Durchlauf — oder setzt einen laufenden fort. */
    public static function starten(): void
    {
        if (self::laeuft()) {
            return;
        }

        $_SESSION[self::SCHLUESSEL] = [
            'submission_id'   => Uuid::v4(),
            'form_started_at' => (string) time(),
            'begonnen_am'     => time(),
            'antworten'       => [],
        ];
    }

    public static function laeuft(): bool
    {
        $stand = $_SESSION[self::SCHLUESSEL] ?? null;

        if (!is_array($stand) || !isset($stand['begonnen_am'])) {
            return false;
        }

        if ((time() - (int) $stand['begonnen_am']) > self::ABLAUF) {
            // Abgelaufen heisst weg, nicht „stillschweigend weiterverwenden".
            unset($_SESSION[self::SCHLUESSEL]);

            return false;
        }

        return true;
    }

    public static function verwerfen(): void
    {
        unset($_SESSION[self::SCHLUESSEL]);
    }

    /** @param array<string,mixed> $werte Nur die Felder des jeweiligen Schritts. */
    public static function merken(array $werte): void
    {
        self::starten();

        $stand = $_SESSION[self::SCHLUESSEL];
        $stand['antworten'] = array_merge(is_array($stand['antworten'] ?? null) ? $stand['antworten'] : [], $werte);

        $_SESSION[self::SCHLUESSEL] = $stand;
    }

    /** @return array<string,mixed> Alle bisher gegebenen Antworten. */
    public static function antworten(): array
    {
        if (!self::laeuft()) {
            return [];
        }

        $antworten = $_SESSION[self::SCHLUESSEL]['antworten'] ?? [];

        return is_array($antworten) ? $antworten : [];
    }

    public static function submissionId(): ?string
    {
        if (!self::laeuft()) {
            return null;
        }

        $wert = $_SESSION[self::SCHLUESSEL]['submission_id'] ?? null;

        return is_string($wert) && $wert !== '' ? $wert : null;
    }

    public static function begonnenAm(): string
    {
        if (!self::laeuft()) {
            return '';
        }

        return (string) ($_SESSION[self::SCHLUESSEL]['form_started_at'] ?? '');
    }

    /**
     * Der höchste Schritt, den die vorliegenden Antworten tragen.
     *
     * Damit lässt sich `/briefing/4` nicht aufrufen, solange Schritt 2 leer ist — und die
     * Ergebnisseite nicht, solange ein Thema fehlt. Ohne diese Prüfung stünde dort eine
     * Empfehlung, die auf halben Angaben beruht.
     */
    public static function erreichbarerSchritt(): int
    {
        $antworten = self::antworten();

        for ($nummer = 1; $nummer <= Bedarfsscheck::SCHRITTE; $nummer++) {
            if (Bedarfsscheck::schrittPruefen($nummer, $antworten) !== []) {
                return $nummer;
            }
        }

        return Bedarfsscheck::SCHRITTE + 1;
    }

    public static function vollstaendig(): bool
    {
        return self::laeuft() && self::erreichbarerSchritt() > Bedarfsscheck::SCHRITTE;
    }
}
