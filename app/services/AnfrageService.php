<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\AnfrageSpeicher;
use Sartu\Data\Db;
use Sartu\Helpers\Validate;

/**
 * Der Anfragedienst — Portal-Lastenheft §4b, Website-Lastenheft §9.
 *
 * **Warum ein eigener Dienst und nicht Formularcode:** In Stufe 1 sollen auch Kundenwebsites
 * Anfragen abliefern können. Dann kommt ein dünner Endpunkt unter `/api/` davor — mit Token
 * und Herkunftsprüfung. **Der Dienst bleibt gleich.** In Stufe 0 gibt es diesen Endpunkt
 * nicht, auch nicht vorbereitend (§4b.1).
 *
 * ## Der Grundsatz hinter den Abweisungen
 *
 * Ein abgewiesener Absender **merkt nichts**. Honigtopf, Zeitregel und eine bereits bekannte
 * `submission_id` führen alle zur Danke-Seite (§4b.2). Wer erfährt, dass sein Versuch
 * erkannt wurde, probiert den nächsten.
 *
 * Fehlermeldungen nennen nie Datenbankfehler, interne Kennungen oder ob eine Adresse schon
 * bekannt ist (Testfall 37).
 */
final class AnfrageService
{
    /** §4b.3: Menschen brauchen für den Bedarfsscheck Minuten. */
    /** Beide Werte stehen jetzt in `Formularschutz` — hier nur noch als Verweis. */
    public const MINDESTDAUER_SEKUNDEN = Formularschutz::MINDESTDAUER_SEKUNDEN;

    /** §4b.2: maximal 64 KB Formulardaten (Testfall 36). */
    public const MAX_BYTES = Formularschutz::MAX_BYTES;

    /** §4b.2: 10 je IP und Stunde, zusätzlich 60 je Stunde gesamt (Testfall 31). */
    public const VERSUCHE_JE_IP = 10;

    public const VERSUCHE_GESAMT = 60;

    private const FENSTER_SEKUNDEN = Formularschutz::FENSTER_SEKUNDEN;

    /**
     * Löschfrist — die Stelle, an der das Lastenheft zweimal verschieden stand.
     *
     * §4 sagt beim Feld `delete_after`: „Eingang + 6 Monate". §4b.4 und §15.1 sagen:
     * abgelehnte nach **6** Monaten, alle übrigen nicht umgewandelten nach **12**. Die
     * Auflösung steht in `BAUFREIGABE.md` als behobener Befund: getrennt nach Fall, und
     * §4b.4 nennt den Grund mit — „die kürzere Frist gilt für den engeren Fall".
     *
     * Beim Anlegen gilt deshalb die längere; erst die Ablehnung verkürzt sie (Testfall 80).
     */
    public const FRIST_MONATE_OFFEN = 12;

    public const FRIST_MONATE_ABGELEHNT = 6;

    public function __construct(
        private readonly ?AnfrageSpeicher $speicher = null,
        private readonly ?Ratenbegrenzung $begrenzung = null,
    ) {
    }

    /**
     * Nimmt eine Anfrage an.
     *
     * @param array<string,mixed> $eingabe   Formularfelder
     * @param array<string,string|null> $herkunft aus `Herkunft::ausSitzung()`
     *
     * @return AnfrageErgebnis
     */
    public function anlegen(array $eingabe, array $herkunft = [], ?string $ip = null): AnfrageErgebnis
    {
        // 1. Größe. Vor allem anderen — was zu groß ist, wird nicht erst geprüft.
        if (Formularschutz::zuGross($eingabe)) {
            return AnfrageErgebnis::abgewiesen(['Ihre Angaben sind zu umfangreich. Bitte kürzen Sie die Freitexte.']);
        }

        // 2. Ratenbegrenzung. Der Hinweis nennt eine Kontaktalternative, keine Technik.
        if (!$this->begrenzung()->erlaubt('anfrage-ip:' . (string) $ip, self::VERSUCHE_JE_IP, self::FENSTER_SEKUNDEN)
            || !$this->begrenzung()->erlaubt('anfrage-gesamt', self::VERSUCHE_GESAMT, self::FENSTER_SEKUNDEN)) {
            return AnfrageErgebnis::begrenzt();
        }

        // 3. Honigtopf und Zeitregel — beide still. Der Absender sieht die Danke-Seite.
        if (Formularschutz::honigtopfGefuellt($eingabe)) {
            return AnfrageErgebnis::stillVerworfen();
        }

        if (!Formularschutz::zeitregelErfuellt(self::text($eingabe, 'form_started_at'))) {
            return AnfrageErgebnis::stillVerworfen();
        }

        $submissionId = self::text($eingabe, 'submission_id');

        if (!Formularschutz::istUuid($submissionId)) {
            return AnfrageErgebnis::stillVerworfen();
        }

        // 4. Doppeleinreichung — deckt Doppelklick, Neuladen und die Zurück-Taste ab.
        if ($this->speicher()->kenntEinreichung($submissionId)) {
            return AnfrageErgebnis::stillVerworfen();
        }

        // 5. Pflichtfelder. Erst ab hier sieht der Absender Fehler — am Feld.
        $fehler = $this->pflichtfelderPruefen($eingabe);

        if ($fehler !== []) {
            return AnfrageErgebnis::abgewiesenAmFeld($fehler);
        }

        $this->begrenzung()->vermerken('anfrage-ip:' . (string) $ip, self::FENSTER_SEKUNDEN);
        $this->begrenzung()->vermerken('anfrage-gesamt', self::FENSTER_SEKUNDEN);

        // 6. Empfehlung und Ampel — SERVERSEITIG. Was im Formular stand, zählt nicht
        //    (Testfall 39).
        $ergebnis = Empfehlung::bestimmen(
            self::liste($eingabe, 'umfangssignale'),
            self::liste($eingabe, 'sonderfunktionen'),
            bestehendeWebsiteUnklar: self::text($eingabe, 'bestehende_website') === 'unsicher',
            zielgruppeUnklar: self::text($eingabe, 'zielgruppe') === 'unklar',
            domainUnklar: self::text($eingabe, 'domainstatus') === 'unsicher',
            festerTermin: self::text($eingabe, 'fester_termin') === 'ja',
        );

        $jetzt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $id = $this->speicher()->anlegen([
            'submission_id'        => $submissionId,
            'submitted_at'         => $jetzt->format('Y-m-d H:i:s'),
            'payload'              => json_encode(self::payload($eingabe), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'first_name'           => self::text($eingabe, 'first_name'),
            'last_name'            => self::text($eingabe, 'last_name'),
            'company'              => self::text($eingabe, 'company'),
            'email'                => mb_strtolower(self::text($eingabe, 'email')),
            'phone'                => self::textOderNull($eingabe, 'phone'),
            'preferred_contact'    => self::text($eingabe, 'preferred_contact'),
            'recommended_package'  => $ergebnis['paket'],
            'flag'                 => $ergebnis['ampel'],
            'status'               => 'neu',
            'b2b_confirmed'        => 1,
            'privacy_confirmed'    => 1,
            'source_ip'            => $ip,
            'branche_vorbelegt'    => self::textOderNull($eingabe, 'branche_vorbelegt'),
            'self_reported_source' => self::textOderNull($eingabe, 'self_reported_source'),
            'delete_after'         => $jetzt->modify('+' . self::FRIST_MONATE_OFFEN . ' months')->format('Y-m-d'),
            ...$this->herkunftsfelder($herkunft),
        ]);

        return AnfrageErgebnis::angelegt($id, $ergebnis['paket'], $ergebnis['ampel'], $ergebnis['gruende']);
    }

    /** Das Löschdatum nach einer Ablehnung — die kürzere Frist für den engeren Fall. */
    public static function frist(string $eingangstag, string $zustand): string
    {
        $monate = $zustand === 'abgelehnt' ? self::FRIST_MONATE_ABGELEHNT : self::FRIST_MONATE_OFFEN;

        return (new \DateTimeImmutable($eingangstag))->modify('+' . $monate . ' months')->format('Y-m-d');
    }

    // ------------------------------------------------------------------ intern

    /** @return array<string,string|null> */
    private function herkunftsfelder(array $herkunft): array
    {
        $felder = [];

        foreach (['landing_page', 'referrer_host', 'utm_source', 'utm_medium', 'utm_campaign',
                  'utm_term', 'utm_content', 'click_id'] as $feld) {
            $wert = $herkunft[$feld] ?? null;
            $felder[$feld] = is_string($wert) && $wert !== '' ? $wert : null;
        }

        return $felder;
    }

    /** @return array<string,string> Fehler je Feldname — die Oberfläche zeigt sie AM Feld. */
    private function pflichtfelderPruefen(array $eingabe): array
    {
        $fehler = [];

        if (!Validate::gefuellt(self::text($eingabe, 'first_name'))
            || !Validate::gefuellt(self::text($eingabe, 'last_name'))) {
            $fehler['name'] = 'Bitte geben Sie Ihren Namen an.';
        }

        if (!Validate::gefuellt(self::text($eingabe, 'company'))) {
            $fehler['company'] = 'Bitte geben Sie Ihr Unternehmen an.';
        }

        if (!Validate::email(self::text($eingabe, 'email'))) {
            $fehler['email'] = 'Bitte geben Sie eine gültige E-Mail-Adresse an, z. B. name@firma.de';
        }

        if (!in_array(self::text($eingabe, 'preferred_contact'), ['email', 'portal'], true)) {
            $fehler['preferred_contact'] = 'Bitte wählen Sie, wie wir Sie erreichen sollen.';
        }

        if (!self::wahr($eingabe, 'b2b_confirmed')) {
            $fehler['b2b_confirmed'] = 'Bitte bestätigen Sie, dass Sie als Unternehmen anfragen. '
                . 'SARTU arbeitet ausschließlich für Unternehmer.';
        }

        if (!self::wahr($eingabe, 'privacy_confirmed')) {
            $fehler['privacy_confirmed'] = 'Bitte bestätigen Sie die Datenschutzhinweise.';
        }

        return $fehler;
    }

    /**
     * §4b.2: „Der Bedarfsscheck darf erweitert werden; unbekannte Felder landen unverändert
     * in `payload`, statt abgewiesen zu werden" (Testfall 38).
     *
     * Nicht in `payload`: die Bestätigungen und die Spamabwehr. Sie stehen als eigene
     * Spalten und haben im Antwortarchiv nichts zu suchen.
     *
     * @return array<string,mixed>
     */
    private static function payload(array $eingabe): array
    {
        $ohne = ['hp_website', 'form_started_at', 'submission_id', '_token'];

        return array_diff_key($eingabe, array_flip($ohne));
    }

    private static function text(array $eingabe, string $feld): string
    {
        $wert = $eingabe[$feld] ?? '';

        return is_string($wert) ? trim($wert) : '';
    }

    private static function textOderNull(array $eingabe, string $feld): ?string
    {
        $wert = self::text($eingabe, $feld);

        return $wert === '' ? null : $wert;
    }

    /** @return list<string> */
    private static function liste(array $eingabe, string $feld): array
    {
        $wert = $eingabe[$feld] ?? [];

        if (!is_array($wert)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($w) => is_string($w) ? $w : null,
            $wert,
        )));
    }

    private static function wahr(array $eingabe, string $feld): bool
    {
        $wert = $eingabe[$feld] ?? null;

        return in_array($wert, ['1', 'ja', 'on', 'true', true, 1], true);
    }

    private function speicher(): AnfrageSpeicher
    {
        return $this->speicher ?? new AnfrageSpeicher();
    }

    private function begrenzung(): Ratenbegrenzung
    {
        return $this->begrenzung ?? new Ratenbegrenzung();
    }
}
