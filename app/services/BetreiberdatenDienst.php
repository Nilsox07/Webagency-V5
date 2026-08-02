<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\AuditProtokoll;
use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Helpers\Validate;

/**
 * Betreiberdaten pruefen und speichern — Portal-Lastenheft §1.4a.
 *
 * Anschrift, Kontaktdaten und Steuerangaben stehen nirgends im Quelltext. Sie liegen als
 * Einstellungen in der Datenbank, weil eine falsche Anschrift im Impressum abmahnfaehig ist
 * und in Minuten korrigierbar sein muss — nicht ueber einen Auslieferungsvorgang.
 *
 * Jede Aenderung erzeugt einen vollstaendigen Pruefeintrag mit altem Wert, neuem Wert und
 * Grund. Es sind rechtlich erhebliche Angaben.
 */
final class BetreiberdatenDienst
{
    /** Aenderungen hieran verlangen einen sichtbaren Hinweis auf Impressum und Rechnungen. */
    private const IMPRESSUMSRELEVANT = ['firmenname', 'rechtsform', 'strasse', 'plz', 'ort', 'land'];

    public function __construct(
        private readonly ?BetreiberdatenSpeicher $speicher = null,
        private readonly ?AuditProtokoll $audit = null,
    ) {
    }

    /**
     * @param array<string,string> $eingabe
     * @return list<string> Klartextfehler. Leer bedeutet: die Eingabe ist gueltig.
     */
    public function pruefen(array $eingabe): array
    {
        $fehler = [];

        foreach (BetreiberdatenSpeicher::PFLICHTFELDER as $feld) {
            if (!Validate::gefuellt($eingabe[$feld] ?? null)) {
                $fehler[] = sprintf(
                    'Bitte füllen Sie das Feld „%s" aus.',
                    BetreiberdatenSpeicher::beschriftung($feld)
                );
            }
        }

        $land = strtoupper(trim($eingabe['land'] ?? ''));

        if (Validate::gefuellt($eingabe['land'] ?? null) && !Validate::land($land)) {
            $fehler[] = 'Das Land wird als zweistelliges Kürzel angegeben, zum Beispiel DE.';
        }

        if (Validate::gefuellt($eingabe['plz'] ?? null) && !Validate::plz($eingabe['plz'] ?? null, $land)) {
            $fehler[] = $land === 'DE'
                ? 'Eine deutsche Postleitzahl hat fünf Ziffern.'
                : 'Die Postleitzahl ist zu lang.';
        }

        if (Validate::gefuellt($eingabe['email'] ?? null) && !Validate::email($eingabe['email'] ?? null)) {
            $fehler[] = 'Die E-Mail-Adresse ist nicht vollständig.';
        }

        // §1.4a: entweder ust_id oder steuernummer. Die Datenbank prueft dasselbe, aber die
        // Meldung dort waere fuer einen Menschen unbrauchbar.
        $ustId = $eingabe['ust_id'] ?? null;
        $steuernummer = $eingabe['steuernummer'] ?? null;

        if (!Validate::gefuellt($ustId) && !Validate::gefuellt($steuernummer)) {
            $fehler[] = 'Tragen Sie entweder die Umsatzsteuer-Identifikationsnummer oder die Steuernummer ein.';
        }

        if (Validate::gefuellt($ustId) && !Validate::ustId($ustId)) {
            $fehler[] = 'Die Umsatzsteuer-Identifikationsnummer beginnt mit dem Länderkürzel, zum Beispiel DE123456789.';
        }

        if (Validate::gefuellt($steuernummer) && !Validate::steuernummer($steuernummer)) {
            $fehler[] = 'Die Steuernummer enthält Zeichen, die dort nicht vorkommen.';
        }

        if (Validate::gefuellt($eingabe['bank_iban'] ?? null) && !Validate::iban($eingabe['bank_iban'] ?? null)) {
            $fehler[] = 'Die IBAN stimmt rechnerisch nicht. Bitte prüfen Sie die Ziffern.';
        }

        return $fehler;
    }

    /**
     * @param array<string,string> $eingabe
     * @return array<string,scalar|null>
     */
    public function aufbereiten(array $eingabe): array
    {
        $werte = [];

        foreach (BetreiberdatenSpeicher::SCHREIBBARE_FELDER as $feld) {
            if (!array_key_exists($feld, $eingabe)) {
                continue;
            }

            if ($feld === 'kleinunternehmer') {
                $werte[$feld] = in_array($eingabe[$feld], ['1', 'ja', 'on', 'true'], true) ? 1 : 0;
                continue;
            }

            $wert = trim($eingabe[$feld]);

            if ($feld === 'land') {
                $wert = strtoupper($wert);
            }

            // Leere Zusatzfelder werden als NULL gespeichert, nicht als ''. Sonst haette
            // die Steuerbedingung einen Wert vor sich, der keiner ist (§4).
            $werte[$feld] = $wert === '' && !in_array($feld, BetreiberdatenSpeicher::PFLICHTFELDER, true)
                ? null
                : $wert;
        }

        return $werte;
    }

    /**
     * Speichert und protokolliert. Der Grund ist Pflicht — §1.4a verlangt ihn ausdruecklich.
     *
     * @param array<string,string> $eingabe
     * @return list<string> Hinweise fuer die Oberflaeche, keine Fehler
     */
    public function speichern(array $eingabe, string $grund, string $akteurBenutzerId, ?string $ip): array
    {
        if (trim($grund) === '') {
            throw new \InvalidArgumentException('Eine Änderung der Betreiberdaten ohne Grund wird nicht gespeichert.');
        }

        $speicher = $this->speicher ?? new BetreiberdatenSpeicher();
        $audit = $this->audit ?? new AuditProtokoll();

        $vorher = $speicher->lesen() ?? [];
        $neu = $this->aufbereiten($eingabe);

        $geaendert = [];
        foreach ($neu as $feld => $wert) {
            $alt = $vorher[$feld] ?? null;
            if ((string) $alt !== (string) $wert) {
                $geaendert[$feld] = ['alt' => $alt, 'neu' => $wert];
            }
        }

        if ($geaendert === []) {
            return [];
        }

        $speicher->aktualisieren($neu);

        foreach ($geaendert as $feld => $wechsel) {
            $audit->schreiben(
                aktion: 'betreiberdaten_geaendert',
                objektart: 'operator_settings',
                objektId: null,
                akteurBenutzerId: $akteurBenutzerId,
                alterWert: $wechsel['alt'] === null ? null : (string) $wechsel['alt'],
                neuerWert: $wechsel['neu'] === null ? null : (string) $wechsel['neu'],
                grund: $grund,
                detail: ['feld' => $feld],
                ip: $ip,
            );
        }

        $hinweise = [];

        if (array_intersect(array_keys($geaendert), self::IMPRESSUMSRELEVANT) !== []) {
            $hinweise[] = 'Name oder Anschrift haben sich geändert. Prüfen Sie Impressum und Rechnungsvorlagen.';
        }

        return $hinweise;
    }
}
