<?php

declare(strict_types=1);

namespace Sartu\Data;

/**
 * Zugriff auf `legal_texts` — Portal-Lastenheft §1.4a und §4.
 *
 * Zwei Leseweisen, und der Unterschied ist der ganze Punkt:
 *
 *   oeffentlich()  liefert nur `status = freigegeben` UND `audience = oeffentlich`.
 *   intern()       liefert alles, auch Entwuerfe — nur im angemeldeten Adminbereich.
 *
 * Testfall 82 prueft, dass ein Text mit `audience = kunde` oeffentlich nicht abrufbar ist.
 * Deshalb gibt es keinen gemeinsamen Aufruf mit einem Schalter: Ein vergessener Schalter
 * waere ein veroeffentlichter Entwurf.
 */
final class RechtstexteSpeicher
{
    public const SLUGS = ['impressum', 'datenschutz', 'agb', 'avv', 'tom'];

    /**
     * Menschliche Beschriftung je Text. Ein Slug ist ein Systemcode — §13 verlangt Klartext,
     * und `tom` sagt niemandem etwas.
     */
    public const BESCHRIFTUNGEN = [
        'impressum'   => 'Impressum',
        'datenschutz' => 'Datenschutzerklärung',
        'agb'         => 'Allgemeine Geschäftsbedingungen',
        'avv'         => 'Auftragsverarbeitungsvertrag',
        'tom'         => 'Technische und organisatorische Maßnahmen',
    ];

    /** Wo ein Text ausgeliefert wird. `avv` und `tom` nur angemeldet (Testfall 82). */
    public const ZIELGRUPPEN = [
        'impressum'   => 'oeffentlich',
        'datenschutz' => 'oeffentlich',
        'agb'         => 'oeffentlich',
        'avv'         => 'kunde',
        'tom'         => 'kunde',
    ];

    public const ZUSTAENDE = ['entwurf', 'in_pruefung', 'freigegeben'];

    /** §2 SARTU_ENTSCHEIDUNGEN_OFFEN: Jeder Entwurf traegt diesen Vermerk am Kopf. */
    public const ENTWURFSVERMERK = 'ENTWURF — NICHT GEPRÜFT, NICHT VERÖFFENTLICHEN';

    public static function beschriftung(string $slug): string
    {
        return self::BESCHRIFTUNGEN[$slug] ?? $slug;
    }

    public function __construct(private readonly ?\PDO $pdo = null)
    {
    }

    /** @return array<string,mixed>|null */
    public function oeffentlich(string $slug): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM legal_texts WHERE slug = ? AND status = ? AND audience = ?'
        );
        $anweisung->execute([$slug, 'freigegeben', 'oeffentlich']);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /** Nur angemeldet: fuer `avv` und `tom` unter /portal/vertrag. */
    public function fuerKunden(string $slug): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM legal_texts WHERE slug = ? AND status = ? AND audience = ?'
        );
        $anweisung->execute([$slug, 'freigegeben', 'kunde']);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /** @return array<string,mixed>|null */
    public function intern(string $slug): ?array
    {
        $anweisung = $this->pdo()->prepare('SELECT * FROM legal_texts WHERE slug = ?');
        $anweisung->execute([$slug]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /** @return list<array<string,mixed>> */
    public function alleIntern(): array
    {
        return $this->pdo()->query('SELECT * FROM legal_texts ORDER BY slug')->fetchAll();
    }

    public function anlegen(string $slug, string $rumpf, string $zielgruppe): string
    {
        $id = Uuid::v4();

        $anweisung = $this->pdo()->prepare(
            'INSERT INTO legal_texts (id, slug, body, status, version, audience) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([$id, $slug, $rumpf, 'entwurf', 1, $zielgruppe]);

        return $id;
    }

    /** Jede inhaltliche Aenderung setzt den Zustand zurueck und zaehlt die Fassung hoch. */
    public function entwurfSpeichern(string $slug, string $rumpf): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE legal_texts SET body = ?, status = ?, released_at = NULL, released_by = NULL, version = version + 1'
            . ' WHERE slug = ?'
        );
        $anweisung->execute([$rumpf, 'entwurf', $slug]);
    }

    public function zustandSetzen(string $slug, string $zustand, ?string $pruefendeStelle): void
    {
        if (!in_array($zustand, self::ZUSTAENDE, true)) {
            throw new \InvalidArgumentException('Unbekannter Zustand fuer einen Rechtstext.');
        }

        // §1.4a: Den Zustand auf `freigegeben` setzen darf nur ein Mensch, mit Datum und
        // Namen der pruefenden Stelle. Kein automatischer Uebergang, keine Voreinstellung.
        if ($zustand === 'freigegeben' && ($pruefendeStelle === null || trim($pruefendeStelle) === '')) {
            throw new \InvalidArgumentException('Eine Freigabe ohne Namen der pruefenden Stelle ist nicht zulaessig.');
        }

        $anweisung = $this->pdo()->prepare(
            'UPDATE legal_texts SET status = ?, released_at = ?, released_by = ? WHERE slug = ?'
        );
        $anweisung->execute([
            $zustand,
            $zustand === 'freigegeben' ? Db::jetzt() : null,
            $zustand === 'freigegeben' ? trim((string) $pruefendeStelle) : null,
            $slug,
        ]);
    }

    /**
     * Fuer die Startsperre (§1.4a): Slugs, die noch nicht freigegeben sind.
     * Ein fehlender Text zaehlt wie ein Entwurf — sonst liesse sich die Sperre durch
     * Weglassen umgehen (Testfall 81).
     *
     * @return list<string>
     */
    public function nichtFreigegebene(): array
    {
        $freigegeben = [];
        foreach ($this->alleIntern() as $text) {
            if ((string) $text['status'] === 'freigegeben') {
                $freigegeben[] = (string) $text['slug'];
            }
        }

        return array_values(array_diff(self::SLUGS, $freigegeben));
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
