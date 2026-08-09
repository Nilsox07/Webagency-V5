<?php

declare(strict_types=1);

namespace Sartu\Services;

use horstoeko\zugferd\ZugferdDocumentBuilder;
use horstoeko\zugferd\ZugferdProfiles;
use horstoeko\zugferd\ZugferdXsdValidator;

/**
 * Der strukturierte Teil einer Rechnung — `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 3.
 *
 * ## Ein Dokument, zwei Lesarten
 *
 * §3: „Jede Rechnung entsteht als **PDF/A-3 mit eingebettetem XML nach EN 16931**. Ein
 * Dokument, zwei Lesarten: Der Mensch sieht das PDF, die Software liest das XML."
 *
 * Diese Klasse baut das XML. Das Einbetten macht `Belegerzeugung`; getrennt, weil das XML
 * auch allein gebraucht wird — §3 erlaubt einen reinen XRechnung-Export als Nebenweg, „aus
 * demselben Datensatz".
 *
 * ## Profil `EN16931`, und kein anderes
 *
 * §3: „Die Profile `MINIMUM` und `BASIC-WL` sind ausgeschlossen — sie sind keine
 * vollwertigen Rechnungen im Sinne der Norm."
 *
 * ## Die Pflichtangaben werden nicht von Hand geprüft
 *
 * §2: „Das Profil `EN16931` erzwingt genau diese Felder; ein Beleg, dem eines fehlt, fällt
 * bei der Prüfung durch. **Die Validierung ist die Prüfung der Pflichtangaben** — nicht eine
 * Prüfung daneben." Deshalb steht hier keine Liste aus § 14 Abs. 4 UStG, die jemand pflegen
 * müsste. Es steht der Aufbau, und danach die Prüfung.
 *
 * ## Geld: Cent hinein, Euro hinaus
 *
 * Gespeichert wird als Integer in Cent (`13_DATENMODELL.md`). Die Norm rechnet in
 * Dezimalbeträgen. Die Umrechnung steht **an einer Stelle** — in `betrag()` — damit sie
 * nicht an sieben Stellen unterschiedlich rundet.
 */
final class ERechnung
{
    /** Der Belegtyp nach UNTDID 1001: 380 Rechnung, 381 Gutschrift/Storno. */
    public const TYP_RECHNUNG = '380';

    public const TYP_STORNO = '381';

    /** Währung. Fremdwährungen stehen auf der Nicht-bauen-Liste (§11). */
    public const WAEHRUNG = 'EUR';

    /** Umsatzsteuer-Kategorie „Regelsatz". §4a: SARTU führt Regelbesteuerung. */
    private const KATEGORIE_REGELSATZ = 'S';

    /** Mengeneinheit „Stück" nach UN/ECE Recommendation 20. */
    private const EINHEIT_STUECK = 'C62';

    /**
     * @param array<string,mixed> $rechnung
     * @param array<string,mixed> $betreiber
     * @param array<string,mixed> $organisation
     * @param list<array{bezeichnung:string,netto:int}> $positionen
     */
    public function __construct(
        private readonly array $rechnung,
        private readonly array $betreiber,
        private readonly array $organisation,
        private readonly array $positionen,
    ) {
    }

    /** Das fertige XML nach EN 16931. */
    public function xml(): string
    {
        return $this->aufbauen()->getContent();
    }

    /**
     * Der aufgebaute Datensatz — fuer das Einbetten ins PDF.
     *
     * `Belegerzeugung` braucht ihn, weil der Einbetter des Pakets den Bauer nimmt und nicht
     * das fertige XML. Es ist **derselbe** Aufbau wie in `xml()` und `pruefen()`; ein zweiter
     * waere ein zweiter Datensatz, und §3 verbietet genau das („zwei Wahrheiten, die
     * auseinanderlaufen koennen").
     */
    public function rohbau(): ZugferdDocumentBuilder
    {
        return $this->aufbauen();
    }

    /**
     * Prüft gegen das Schema der Norm — §3, „Prüfung vor dem Versand".
     *
     * @return list<string> leer, wenn der Beleg besteht. Sonst die verletzten Regeln.
     */
    public function pruefen(): array
    {
        $pruefer = new ZugferdXsdValidator($this->aufbauen());
        $pruefer->validate();

        if ($pruefer->validationPased()) {
            return [];
        }

        return array_values(array_map(
            static fn ($fehler): string => trim((string) $fehler),
            $pruefer->validationErrors(),
        ));
    }

    /** Baut den Datensatz auf. Jeder Aufruf baut neu — der Aufbau hat keinen Zustand. */
    private function aufbauen(): ZugferdDocumentBuilder
    {
        $netto = (int) $this->rechnung['net_cents'];
        $steuer = (int) $this->rechnung['vat_cents'];
        $brutto = (int) $this->rechnung['gross_cents'];
        $storno = ($this->rechnung['cancels_invoice_id'] ?? null) !== null;

        $bauer = ZugferdDocumentBuilder::createNew(ZugferdProfiles::PROFILE_EN16931);

        $bauer->setDocumentInformation(
            (string) $this->rechnung['number'],
            $storno ? self::TYP_STORNO : self::TYP_RECHNUNG,
            $this->ausstellungsdatum(),
            self::WAEHRUNG,
        );

        // Verkäufer — Name, Anschrift und **eine** Steuernummer. Welche von beiden gesetzt
        // ist, prüft die Startsperre; hier wird genommen, was dasteht.
        $bauer->setDocumentSeller(self::text($this->betreiber, 'firmenname'))
            ->setDocumentSellerAddress(
                self::text($this->betreiber, 'strasse'),
                '',
                '',
                self::text($this->betreiber, 'plz'),
                self::text($this->betreiber, 'ort'),
                self::text($this->betreiber, 'land') === '' ? 'DE' : self::text($this->betreiber, 'land'),
            );

        if (self::text($this->betreiber, 'ust_id') !== '') {
            $bauer->addDocumentSellerTaxRegistration('VA', self::text($this->betreiber, 'ust_id'));
        }

        if (self::text($this->betreiber, 'steuernummer') !== '') {
            $bauer->addDocumentSellerTaxRegistration('FC', self::text($this->betreiber, 'steuernummer'));
        }

        $bauer->setDocumentBuyer(self::text($this->organisation, 'legal_name'))
            ->setDocumentBuyerAddress(
                self::text($this->organisation, 'street'),
                '',
                '',
                self::text($this->organisation, 'postal_code'),
                self::text($this->organisation, 'city'),
                'DE',
            );

        if (self::text($this->organisation, 'vat_id') !== '') {
            $bauer->addDocumentBuyerTaxRegistration('VA', self::text($this->organisation, 'vat_id'));
        }

        // §2: „Zeitpunkt der Leistung; bei Anzahlungen der Zeitraum." Ohne eigene Angabe ist
        // das Leistungsdatum das Ausstellungsdatum — die Norm verlangt einen Wert, und ein
        // erfundener Zeitraum wäre schlechter als der zutreffende Tag.
        $bauer->setDocumentSupplyChainEvent($this->ausstellungsdatum());

        if (($this->rechnung['due_date'] ?? null) !== null) {
            $bauer->addDocumentPaymentTerm(
                'Zahlbar bis ' . \Sartu\Helpers\Format::datum((string) $this->rechnung['due_date']),
                new \DateTime((string) $this->rechnung['due_date']),
            );
        }

        $nummer = 1;

        foreach ($this->positionen as $position) {
            $bauer->addNewPosition((string) $nummer)
                ->setDocumentPositionProductDetails($position['bezeichnung'])
                ->setDocumentPositionNetPrice(self::betrag($position['netto']))
                ->setDocumentPositionQuantity(1, self::EINHEIT_STUECK)
                ->addDocumentPositionTax(self::KATEGORIE_REGELSATZ, 'VAT', Preise::UST_PROZENT)
                ->setDocumentPositionLineSummation(self::betrag($position['netto']));

            ++$nummer;
        }

        $bauer->addDocumentTax(
            self::KATEGORIE_REGELSATZ,
            'VAT',
            self::betrag($netto),
            self::betrag($steuer),
            Preise::UST_PROZENT,
        );

        $bauer->setDocumentSummation(
            self::betrag($brutto),
            self::betrag($brutto - (int) ($this->rechnung['paid_cents'] ?? 0)),
            self::betrag($netto),
            0.0,
            0.0,
            self::betrag($netto),
            self::betrag($steuer),
            null,
            self::betrag((int) ($this->rechnung['paid_cents'] ?? 0)),
        );

        return $bauer;
    }

    /**
     * Das Ausstellungsdatum — Pflichtangabe nach § 14 Abs. 4 UStG.
     *
     * Fehlt `issued_at`, ist der Beleg nicht ausgestellt. Dann wird **nicht** ersatzweise
     * `created_at` genommen: Das Anlagedatum eines Entwurfs ist kein Ausstellungsdatum, und
     * ein falsches Datum auf einer Rechnung ist schlimmer als ein Abbruch.
     */
    private function ausstellungsdatum(): \DateTime
    {
        $wert = $this->rechnung['issued_at'] ?? null;

        if (!is_string($wert) || $wert === '') {
            throw new \RuntimeException(
                'Die Rechnung hat kein Ausstellungsdatum. Ein Beleg entsteht erst mit dem Versand.'
            );
        }

        return new \DateTime($wert);
    }

    /** Cent in Euro — an **einer** Stelle, damit nicht sieben Stellen verschieden runden. */
    private static function betrag(int $cent): float
    {
        return round($cent / 100, 2);
    }

    /** @param array<string,mixed> $zeile */
    private static function text(array $zeile, string $feld): string
    {
        $wert = $zeile[$feld] ?? '';

        return is_scalar($wert) ? trim((string) $wert) : '';
    }
}
