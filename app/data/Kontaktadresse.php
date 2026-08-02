<?php

declare(strict_types=1);

namespace Sartu\Data;

/**
 * Die Kontaktadresse einer Organisation — mehr nicht.
 *
 * **Warum ausserhalb beider Zugriffsschichten:** Eine Projektmail geht auch dann raus, wenn
 * niemand angemeldet ist — der Faelligkeitslauf verschickt Zahlungserinnerungen nachts ueber
 * die Befehlszeile. Sie durch die Adminschicht zu fuehren hiesse, einen Adminnachweis zu
 * erfinden, den niemand erbracht hat (§3 Regel 2).
 *
 * Dieselbe Loesung wie bei `AnmeldeKonten`, `ProjektZustand` und `Faelligkeiten`: absichtlich
 * schmal. Diese Klasse liefert **eine** Spalte einer Zeile, deren Schluessel der Aufrufer
 * bereits geprueft hat. Sie kann nichts auflisten und nichts aendern — es gibt hier keinen
 * Weg, aus einer fehlenden Organisation „alle" zu machen.
 */
final class Kontaktadresse
{
    public function __construct(private readonly ?\PDO $pdo = null)
    {
    }

    public function finden(string $organisationId): ?string
    {
        if ($organisationId === '') {
            return null;
        }

        $anweisung = $this->pdo()->prepare('SELECT contact_email FROM organizations WHERE id = ?');
        $anweisung->execute([$organisationId]);

        $adresse = $anweisung->fetchColumn();

        return is_string($adresse) && $adresse !== '' ? $adresse : null;
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
