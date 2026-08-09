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
        'benachrichtigung_email',
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

    /**
     * Menschliche Beschriftung je Feld.
     *
     * Sie steht hier und nicht in einer Maske, weil drei Stellen dieselbe brauchen: das
     * Formular, die Fehlermeldung beim Speichern und die Startsperre. Drei Kopien laufen
     * auseinander, und die auseinandergelaufene zeigt dann `inhaltlich_verantwortlich`.
     */
    public const BESCHRIFTUNGEN = [
        'firmenname'                => 'Firmenname',
        'rechtsform'                => 'Rechtsform',
        'strasse'                   => 'Straße und Hausnummer',
        'plz'                       => 'Postleitzahl',
        'ort'                       => 'Ort',
        'land'                      => 'Land',
        'telefon'                   => 'Telefonnummer',
        'email'                     => 'E-Mail-Adresse',
        'benachrichtigung_email'    => 'E-Mail für Benachrichtigungen',
        'ust_id'                    => 'Umsatzsteuer-Identifikationsnummer',
        'steuernummer'              => 'Steuernummer',
        'registergericht'           => 'Registergericht',
        'registernummer'            => 'Registernummer',
        'inhaltlich_verantwortlich' => 'Inhaltlich verantwortlich',
        'bank_iban'                 => 'IBAN',
        'bank_bic'                  => 'BIC',
        'bank_institut'             => 'Bank',
        'kleinunternehmer'          => 'Kleinunternehmer nach § 19 UStG',
    ];

    public static function beschriftung(string $feld): string
    {
        return self::BESCHRIFTUNGEN[$feld] ?? $feld;
    }

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

    /**
     * Die beiden Schluesselfelder des Zahlungsdienstes — `18_BELEGE_UND_ZAHLUNG.md` §6.
     *
     * **Sie stehen mit Absicht NICHT in `SCHREIBBARE_FELDER`.** Diese Liste speist das
     * Betriebsformular; ein Geheimnis darin waere ein Geheimnis in einer Maske, die es
     * anzeigt, und in einer Fehlermeldung, die den abgewiesenen Wert wiederholt. Sie haben
     * deshalb ihren eigenen, engen Schreibweg — und keinen eigenen Leseweg, der den Klartext
     * herausgibt: `schluesselLesen()` gibt den verschluesselten Kasten, entschluesseln kann
     * ihn nur `Services\Zahlungsschluessel`.
     */
    public const SCHLUESSELFELDER = ['mollie_key_test', 'mollie_key_live'];

    /**
     * **Der Spaltenname wird nicht verkettet, sondern ausgewaehlt.**
     *
     * Ein Spaltenname laesst sich nicht als Parameter binden — die uebliche Antwort darauf
     * ist eine Weissliste und danach eine Verkettung. `PreparedStatementsTest` weist genau
     * dieses Muster zurueck, und zwar zu Recht: Die Weissliste steht dann eine Zeile ueber
     * der Verkettung, und beim naechsten Feld steht sie zwei Zeilen darueber.
     *
     * Zwei Felder, zwei feste Anweisungen. Kommt ein drittes dazu, faellt es hier auf.
     */
    public function schluesselSetzen(string $feld, ?string $kasten): void
    {
        // Kein „unbekanntes Feld wird ignoriert": Der Aufrufer glaubte, etwas zu speichern.
        // Ein stilles Nichts waere ein Geheimnis, das niemand hinterlegt hat.
        $sql = match ($feld) {
            'mollie_key_test' => 'UPDATE operator_settings SET mollie_key_test = ? WHERE singleton = 1',
            'mollie_key_live' => 'UPDATE operator_settings SET mollie_key_live = ? WHERE singleton = 1',
            default           => throw new \InvalidArgumentException('Unbekanntes Schluesselfeld: ' . $feld),
        };

        $anweisung = $this->pdo()->prepare($sql);
        $anweisung->execute([$kasten]);
    }

    /** Der verschluesselte Kasten, nicht der Schluessel. */
    public function schluesselLesen(string $feld): ?string
    {
        $sql = match ($feld) {
            'mollie_key_test' => 'SELECT mollie_key_test FROM operator_settings WHERE singleton = 1',
            'mollie_key_live' => 'SELECT mollie_key_live FROM operator_settings WHERE singleton = 1',
            default           => throw new \InvalidArgumentException('Unbekanntes Schluesselfeld: ' . $feld),
        };

        $wert = $this->pdo()->query($sql)->fetchColumn();

        return is_string($wert) && $wert !== '' ? $wert : null;
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
