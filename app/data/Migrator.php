<?php

declare(strict_types=1);

namespace Sartu\Data;

/**
 * Migrationen nach Portal-Lastenheft §1.5 „Schritt 3 im Detail" und §1.5a.
 *
 * Der Abschnitt ist dort ungewoehnlich ausfuehrlich, und zwar aus einem technischen Grund:
 * MySQL und MariaDB fuehren bei CREATE TABLE, ALTER TABLE, DROP TABLE und CREATE INDEX ein
 * implizites Commit aus. Eine offene Transaktion endet, BEVOR der Befehl laeuft. Ein ROLLBACK
 * nach einer gescheiterten Migration nimmt die vorher gelaufenen Tabellen NICHT zurueck.
 *
 * Deshalb gibt es hier keine Transaktion um den Gesamtlauf, kein down und keinen
 * Reparaturknopf — er muesste raten. Stattdessen: Vorpruefung, Einzelausfuehrung, Eintrag
 * unmittelbar nach jedem Erfolg, Pruefsumme, Wiederanlauf.
 */
final class Migrator
{
    public function __construct(
        private readonly \PDO $pdo,
        private readonly string $verzeichnis,
    ) {
    }

    /** Wird als Erstes angelegt, vor jeder Fachtabelle. Zaehlt nicht zu den zwanzig Tabellen. */
    public function protokolltabelleAnlegen(): void
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS schema_migrations ('
            . 'version VARCHAR(100) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,'
            . 'checksum CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,'
            . 'applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,'
            . 'duration_ms INT UNSIGNED NOT NULL'
            . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
    }

    public function protokolltabelleVorhanden(): bool
    {
        $anweisung = $this->pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?'
        );
        $anweisung->execute(['schema_migrations']);

        return (int) $anweisung->fetchColumn() > 0;
    }

    /** @return list<string> Tabellennamen der aktuellen Datenbank. */
    public function tabellen(): array
    {
        $anweisung = $this->pdo->query(
            'SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE() ORDER BY table_name'
        );

        return array_map(static fn (array $z) => (string) reset($z), $anweisung->fetchAll());
    }

    public function datenbankIstLeer(): bool
    {
        return $this->tabellen() === [];
    }

    /** @return list<array{version:string,pfad:string,checksum:string}> */
    public function dateien(): array
    {
        $gefunden = glob($this->verzeichnis . '/*.sql');
        $gefunden = $gefunden === false ? [] : $gefunden;
        sort($gefunden, SORT_STRING);

        $dateien = [];
        foreach ($gefunden as $pfad) {
            $inhalt = file_get_contents($pfad);
            if ($inhalt === false) {
                throw new MigrationFehler(sprintf('Die Migrationsdatei %s ist nicht lesbar.', basename($pfad)));
            }

            $dateien[] = [
                'version'  => basename($pfad, '.sql'),
                'pfad'     => $pfad,
                'checksum' => hash('sha256', $inhalt),
            ];
        }

        return $dateien;
    }

    /** @return array<string,array{checksum:string,applied_at:string}> */
    public function eingetragene(): array
    {
        if (!$this->protokolltabelleVorhanden()) {
            return [];
        }

        $zeilen = $this->pdo->query('SELECT version, checksum, applied_at FROM schema_migrations ORDER BY version')->fetchAll();

        $eintraege = [];
        foreach ($zeilen as $zeile) {
            $eintraege[(string) $zeile['version']] = [
                'checksum'   => (string) $zeile['checksum'],
                'applied_at' => (string) $zeile['applied_at'],
            ];
        }

        return $eintraege;
    }

    /**
     * Prueft jede bereits eingetragene Migration gegen ihre Datei.
     *
     * Eine Abweichung bedeutet: Jemand hat eine ausgelieferte Migration nachtraeglich
     * geaendert. Der Datenbankstand ist dann unbekannt — deshalb Abbruch mit Nennung der
     * Datei, nicht Warnung.
     */
    public function pruefsummenPruefen(): void
    {
        $dateien = [];
        foreach ($this->dateien() as $datei) {
            $dateien[$datei['version']] = $datei['checksum'];
        }

        foreach ($this->eingetragene() as $version => $eintrag) {
            if (!isset($dateien[$version])) {
                throw new MigrationFehler(sprintf(
                    'Die eingetragene Migration %s.sql fehlt im Verzeichnis. Der Datenbankstand ist damit nicht mehr nachvollziehbar.',
                    $version
                ));
            }

            if (!hash_equals($eintrag['checksum'], $dateien[$version])) {
                throw new MigrationFehler(sprintf(
                    'Die Migration %s.sql wurde nach dem Einspielen geaendert. Der Datenbankstand ist unbekannt. Abbruch.',
                    $version
                ));
            }
        }
    }

    /** @return list<array{version:string,pfad:string,checksum:string}> */
    public function offene(): array
    {
        $eingetragene = $this->eingetragene();

        return array_values(array_filter(
            $this->dateien(),
            static fn (array $datei) => !isset($eingetragene[$datei['version']])
        ));
    }

    /**
     * Vorpruefung vor der ersten Migration (§1.5).
     *
     * @param bool $leereDatenbankVerlangt true bei der Ersteinrichtung, false bei §1.5a
     * @return list<string> Klartextmeldungen. Leer bedeutet: alles in Ordnung.
     */
    public function vorpruefung(bool $leereDatenbankVerlangt): array
    {
        $fehler = [];

        if ($leereDatenbankVerlangt && !$this->datenbankIstLeer()) {
            $fehler[] = 'Die Datenbank ist nicht leer. Die Einrichtung migriert nicht in fremden Bestand hinein. '
                . 'Vorhandene Tabellen: ' . implode(', ', $this->tabellen()) . '.';
        }

        $zeichensatz = $this->pdo->query(
            'SELECT default_character_set_name, default_collation_name FROM information_schema.schemata WHERE schema_name = DATABASE()'
        )->fetch();

        if (!is_array($zeichensatz) || (string) $zeichensatz['default_character_set_name'] !== 'utf8mb4') {
            $fehler[] = 'Der Zeichensatz der Datenbank ist nicht utf8mb4.';
        } elseif (!str_starts_with((string) $zeichensatz['default_collation_name'], 'utf8mb4_')) {
            $fehler[] = 'Die Kollation der Datenbank passt nicht zu utf8mb4.';
        }

        $zeitzone = (string) $this->pdo->query('SELECT @@session.time_zone')->fetchColumn();
        if ($zeitzone !== '+00:00') {
            $fehler[] = sprintf('Die Verbindung steht auf der Zeitzone %s statt auf +00:00 (§4.0).', $zeitzone);
        }

        foreach ($this->fehlendeRechte() as $recht) {
            $fehler[] = sprintf('Dem Datenbankbenutzer fehlt das Recht %s.', $recht);
        }

        return $fehler;
    }

    /** @return list<string> */
    public function fehlendeRechte(): array
    {
        $benoetigt = ['CREATE', 'ALTER', 'INDEX', 'REFERENCES'];

        try {
            $zeilen = $this->pdo->query('SHOW GRANTS FOR CURRENT_USER()')->fetchAll(\PDO::FETCH_NUM);
        } catch (\PDOException) {
            // Kann der Benutzer seine eigenen Rechte nicht lesen, wird nicht geraten.
            // Ein fehlendes Recht faellt dann bei der ersten Migration auf, mit Klartextmeldung.
            return [];
        }

        $gewaehrt = strtoupper(implode(' ', array_map(static fn (array $z) => (string) $z[0], $zeilen)));

        if (str_contains($gewaehrt, 'ALL PRIVILEGES')) {
            return [];
        }

        return array_values(array_filter($benoetigt, static fn (string $r) => !str_contains($gewaehrt, $r)));
    }

    /**
     * Fuehrt eine einzelne Migration aus und traegt sie unmittelbar nach Erfolg ein.
     *
     * Kein Sammel-Eintrag am Ende: Bricht der Lauf ab, muss der Wiederanlauf genau wissen,
     * was schon gelaufen ist.
     *
     * @param array{version:string,pfad:string,checksum:string} $datei
     */
    public function ausfuehren(array $datei): void
    {
        $sql = file_get_contents($datei['pfad']);
        if ($sql === false) {
            throw new MigrationFehler(sprintf('Die Migrationsdatei %s.sql ist nicht lesbar.', $datei['version']));
        }

        $start = hrtime(true);

        try {
            $this->pdo->exec($sql);
        } catch (\PDOException $fehler) {
            throw new MigrationFehler(
                sprintf(
                    "Migration %s.sql ist gescheitert.\nMeldung des Datenbankservers: %s",
                    $datei['version'],
                    $fehler->getMessage()
                ),
                $datei['version'],
                $fehler
            );
        }

        $dauer = (int) round((hrtime(true) - $start) / 1_000_000);

        $eintrag = $this->pdo->prepare(
            'INSERT INTO schema_migrations (version, checksum, applied_at, duration_ms) VALUES (?, ?, ?, ?)'
        );
        $eintrag->execute([$datei['version'], $datei['checksum'], Db::jetzt(), $dauer]);
    }

    /**
     * Spielt alle offenen Migrationen ein, einzeln.
     *
     * @param null|callable(string):void $melden
     * @return list<string> eingespielte Versionen
     */
    public function offeneEinspielen(?callable $melden = null): array
    {
        $this->pruefsummenPruefen();

        $eingespielt = [];
        foreach ($this->offene() as $datei) {
            $this->ausfuehren($datei);
            $eingespielt[] = $datei['version'];
            if ($melden !== null) {
                $melden($datei['version']);
            }
        }

        return $eingespielt;
    }
}
