<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Helpers\Env;

/**
 * Die Anbindung an Mollie — `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 6.
 *
 * ## Warum von Hand und nicht ueber das Paket des Anbieters
 *
 * Freigegeben sind **zwei** zusaetzliche Pakete: der ZUGFeRD-Erzeuger und der
 * HTML-nach-PDF-Wandler. Ein drittes braucht eine Entscheidung des Betreibers. Gebraucht
 * werden hier zwei Aufrufe an eine REST-Schnittstelle mit JSON — das ist kein Paket wert,
 * und `ext-curl` liegt ohnehin im Bild.
 *
 * ## Was hier NICHT steht
 *
 * Keine Rueckkehrbehandlung. §6: „Der Zahlungsstatus wird nie aus einer Rueckkehr-URL
 * abgeleitet." Die Rueckkehradresse wird an Mollie **uebergeben**, damit der Kunde wieder in
 * seinem Bereich landet; gelesen wird von dort nichts.
 *
 * Kein Zwischenspeicher. Ein zwischengespeicherter Zahlungszustand waere eine zweite
 * Wahrheit neben `invoices` — und die aeltere von beiden gewinnt irgendwann.
 *
 * ## Betraege
 *
 * Mollie rechnet in Dezimalbetraegen als Zeichenkette („11.90"), gespeichert wird in Cent.
 * Die Umrechnung steht an **einer** Stelle je Richtung, aus demselben Grund wie in
 * `ERechnung`.
 */
final class Mollie implements Zahlungsdienst
{
    public const BASIS = 'https://api.mollie.com/v2';

    /** Sekunden. Ein Webhook wartet nicht ewig, und Mollie stellt bei Zeitablauf erneut zu. */
    public const ZEITGRENZE = 10;

    public function __construct(
        private readonly ?Zahlungsschluessel $schluessel = null,
        private readonly string $basis = self::BASIS,
    ) {
    }

    public function zahlungAnlegen(
        int $betragCent,
        string $waehrung,
        string $referenz,
        string $rueckkehr,
        string $webhook,
    ): array {
        $antwort = $this->aufrufen('POST', '/payments', [
            'amount'      => ['currency' => $waehrung, 'value' => self::betrag($betragCent)],
            'description' => 'Rechnung ' . $referenz,
            'redirectUrl' => $rueckkehr,
            'webhookUrl'  => $webhook,
            'metadata'    => ['rechnung' => $referenz],
        ]);

        $kennung = is_string($antwort['id'] ?? null) ? $antwort['id'] : '';

        if ($kennung === '') {
            throw new ZahlungsdienstFehler('Der Zahlungsdienst hat keine Zahlungskennung zurückgegeben.');
        }

        $adresse = $antwort['_links']['checkout']['href'] ?? null;

        return [
            'kennung' => $kennung,
            'adresse' => is_string($adresse) && $adresse !== '' ? $adresse : null,
        ];
    }

    public function zahlungLesen(string $kennung): array
    {
        $antwort = $this->aufrufen('GET', '/payments/' . rawurlencode($kennung));

        // Gelesen wird der **abgerufene** Betrag, nicht der bestellte: Was zurueckkommt, ist
        // die Aussage des Dienstes, und genau sie wird in Schritt 5 gegen die Rechnung
        // geprueft. Bei bezahlten Zahlungen fuehrt Mollie `amount` weiter; `settlementAmount`
        // waere der Betrag nach Gebuehren und gehoert nicht in eine Forderung.
        $betrag = $antwort['amount'] ?? [];

        return [
            'kennung'      => is_string($antwort['id'] ?? null) ? $antwort['id'] : $kennung,
            'zustand'      => is_string($antwort['status'] ?? null) ? $antwort['status'] : 'unbekannt',
            'betrag_cents' => self::cent(is_array($betrag) ? (string) ($betrag['value'] ?? '0') : '0'),
            'waehrung'     => is_array($betrag) ? (string) ($betrag['currency'] ?? '') : '',
            'bezahlt_am'   => is_string($antwort['paidAt'] ?? null) ? $antwort['paidAt'] : null,
            'referenz'     => is_string($antwort['metadata']['rechnung'] ?? null)
                ? $antwort['metadata']['rechnung']
                : null,
        ];
    }

    // ---------------------------------------------------------------- intern

    /**
     * @param array<string,mixed>|null $rumpf
     * @return array<string,mixed>
     */
    private function aufrufen(string $methode, string $pfad, ?array $rumpf = null): array
    {
        $schluessel = $this->schluessel()->klartext();

        if ($schluessel === null) {
            throw new ZahlungsdienstFehler(
                'Für diese Umgebung ist kein Zahlungsschlüssel hinterlegt. '
                . 'Er wird im Adminbereich unter „Ersteinrichtung" eingetragen.'
            );
        }

        $verbindung = curl_init($this->basis . $pfad);

        if ($verbindung === false) {
            throw new ZahlungsdienstFehler('Die Verbindung zum Zahlungsdienst liess sich nicht aufbauen.');
        }

        curl_setopt_array($verbindung, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $methode,
            CURLOPT_TIMEOUT        => self::ZEITGRENZE,
            // Kein `CURLOPT_SSL_VERIFYPEER => false`, auch nicht lokal. Ein Schluessel geht
            // ueber diese Leitung.
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $schluessel,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ]);

        if ($rumpf !== null) {
            curl_setopt($verbindung, CURLOPT_POSTFIELDS, json_encode($rumpf, JSON_UNESCAPED_UNICODE));
        }

        $ausgabe = curl_exec($verbindung);
        $status = (int) curl_getinfo($verbindung, CURLINFO_RESPONSE_CODE);
        $fehlertext = curl_error($verbindung);

        curl_close($verbindung);

        if (!is_string($ausgabe)) {
            // **Der Fehlertext von curl wird nicht durchgereicht.** Er kann die aufgerufene
            // Adresse samt Kopfzeilen enthalten, und in einer davon steht der Schluessel.
            throw new ZahlungsdienstFehler(
                'Der Zahlungsdienst war nicht erreichbar (' . ($fehlertext === '' ? 'Zeitablauf' : 'Verbindungsfehler') . ').'
            );
        }

        $daten = json_decode($ausgabe, true);

        if (!is_array($daten)) {
            throw new ZahlungsdienstFehler('Die Antwort des Zahlungsdienstes war nicht lesbar.');
        }

        if ($status >= 400) {
            $meldung = is_string($daten['detail'] ?? null) ? $daten['detail'] : 'Der Zahlungsdienst hat abgelehnt.';

            throw new ZahlungsdienstFehler($meldung . ' (' . $status . ')');
        }

        return $daten;
    }

    /** Cent nach Mollie: zwei Nachkommastellen, Punkt als Trenner. */
    private static function betrag(int $cent): string
    {
        return number_format($cent / 100, 2, '.', '');
    }

    /** Mollie nach Cent. Ueber `round`, nicht ueber `(int)` — `(int) (11.90 * 100)` ist 1189. */
    private static function cent(string $betrag): int
    {
        return (int) round(((float) $betrag) * 100);
    }

    private function schluessel(): Zahlungsschluessel
    {
        return $this->schluessel ?? new Zahlungsschluessel();
    }

    /** Die Adresse, die Mollie anruft. Aus `BASE_URL`, damit sie zur Umgebung passt. */
    public static function webhookAdresse(): string
    {
        return rtrim(Env::get('BASE_URL', '') ?? '', '/') . '/api/zahlungen/mollie';
    }

    public static function rueckkehrAdresse(): string
    {
        return rtrim(Env::get('BASE_URL', '') ?? '', '/') . '/portal/rechnungen';
    }
}
