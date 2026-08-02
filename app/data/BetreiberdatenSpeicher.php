<?php

declare(strict_types=1);

namespace Sartu\Data;

/**
 * Zugriff auf `operator_settings` — Portal-Lastenheft §1.4a und §4.
 *
 * Die Tabelle hat keine `organization_id`. Sie sind die Daten des Betreibers, nicht die
 * eines Mandanten — die Trennung nach §3 Regel 1 beruehrt sie nicht. Lesen darf deshalb
 * auch der oeffentliche Bereich (Impressum, Fussbereich, Testfall 83).
 *
 * Geschrieben wird ausschliesslich durch die Ersteinrichtung (einmal) und den Adminbereich.
 * Die Tabelle kennt genau eine Zeile: kein INSERT ausser dem ersten, kein DELETE.
 */
final class BetreiberdatenSpeicher
{
    /** Die sieben Pflichtfelder aus §1.5 Schritt 6. */
    public const PFLICHTFELDER = [
        'firmenname',
        'strasse',
        'plz',
        'ort',
        'land',
        'email',
        'inhaltlich_verantwortlich',
    ];

    public const SCHREIBBARE_FELDER = [
        'firmenname',
        'rechtsform',
        'strasse',
        'plz',
        'ort',
        'land',
        'telefon',
        'email',
        'ust_id',
        'steuernummer',
        'registergericht',
        'registernummer',
        'inhaltlich_verantwortlich',
        'bank_iban',
        'bank_bic',
        'bank_institut',
        'kleinunternehmer',
    ];

    public function __construct(private readonly ?\PDO $pdo = null)
    {
    }

    /** @return array<string,mixed>|null */
    public function lesen(): ?array
    {
        $zeile = $this->pdo()->query('SELECT * FROM operator_settings WHERE singleton = 1')->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    public function vorhanden(): bool
    {
        return $this->lesen() !== null;
    }

    /**
     * Legt die eine Zeile an. §1.5 Schritt 6 ist der einzige Aufrufer.
     *
     * @param array<string,scalar|null> $werte
     */
    public function anlegen(array $werte): string
    {
        $id = Uuid::v4();
        $spalten = ['id', 'singleton'];
        $platzhalter = ['?', '1'];
        $parameter = [$id];

        foreach (self::SCHREIBBARE_FELDER as $feld) {
            if (!array_key_exists($feld, $werte)) {
                continue;
            }
            $spalten[] = $feld;
            $platzhalter[] = '?';
            $parameter[] = $werte[$feld];
        }

        $anweisung = $this->pdo()->prepare(sprintf(
            'INSERT INTO operator_settings (%s) VALUES (%s)',
            implode(', ', $spalten),
            implode(', ', $platzhalter),
        ));
        $anweisung->execute($parameter);

        return $id;
    }

    /** @param array<string,scalar|null> $werte */
    public function aktualisieren(array $werte): void
    {
        $zuweisungen = [];
        $parameter = [];

        foreach (self::SCHREIBBARE_FELDER as $feld) {
            if (!array_key_exists($feld, $werte)) {
                continue;
            }
            $zuweisungen[] = $feld . ' = ?';
            $parameter[] = $werte[$feld];
        }

        if ($zuweisungen === []) {
            return;
        }

        $anweisung = $this->pdo()->prepare(
            'UPDATE operator_settings SET ' . implode(', ', $zuweisungen) . ' WHERE singleton = 1'
        );
        $anweisung->execute($parameter);
    }

    /** §1.5 Schritt 8 — die eine Haelfte der Installationssperre. */
    public function einrichtungAbschliessen(): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE operator_settings SET setup_completed_at = ? WHERE singleton = 1 AND setup_completed_at IS NULL'
        );
        $anweisung->execute([Db::jetzt()]);
    }

    public function einrichtungAbgeschlossen(): bool
    {
        try {
            $wert = $this->pdo()->query('SELECT setup_completed_at FROM operator_settings WHERE singleton = 1')->fetchColumn();
        } catch (\PDOException) {
            // Tabelle gibt es noch nicht — die Einrichtung laeuft also gerade erst an.
            return false;
        }

        return is_string($wert) && $wert !== '';
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
