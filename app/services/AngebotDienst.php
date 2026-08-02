<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\Admin\AdminAngebote;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Admin\AdminProjekte;
use Sartu\Data\AuditProtokoll;
use Sartu\Helpers\Validate;

/**
 * Angebote anlegen und senden — Portal-Lastenheft §4, §4c, §5.1a und §5.2.
 *
 * > „Ein angenommenes Angebot ist die vertragliche Grundlage. Es muss deshalb alles
 * > enthalten, was später strittig werden kann — nicht nur den Preis."
 *
 * ## Die vier Prüfungen, ohne die nicht gespeichert wird
 *
 * | Prüfung | Quelle | Warum sie hier steht und nicht nur in der Datenbank |
 * |---|---|---|
 * | `first_year = one_time + 12 × monthly` | §4 Prüfregel Erstjahreswert | Die Datenbank sagt „Constraint verletzt". §4 verlangt eine Meldung, die den erwarteten Betrag nennt |
 * | `custom` nur beim Sonderprojekt | §4 Prüfregel Zahlungsplan | dito |
 * | Pflichtfelder nach `trim()` gefüllt | §4 — `NOT NULL` erlaubt `''` | Die Datenbank nimmt die leere Zeichenkette an |
 * | BFSG-Sperre | §4c | Steht in **keiner** Bedingung der Datenbank. Es geht um ein Bußgeld bis 100.000 € |
 *
 * Die ersten beiden stehen **zusätzlich** als Prüfbedingung in `migrations/012_offers.sql`.
 * Das ist keine Doppelung: Hier steht die lesbare Meldung, dort der zweite Schreibweg, den
 * es irgendwann gibt.
 *
 * ## Was der Dienst NICHT tut
 *
 * **Er erfindet keine Angebotsnummer.** §4a: Nummernkreis `AN-JJJJ-NNN`, „in Stufe 0 vom
 * Admin eingegeben, Eindeutigkeit erzwingt die Datenbank".
 *
 * **Er setzt kein Gültigkeitsdatum.** §4c belegt drei Texte und den Lieferkorridor vor —
 * `valid_until` steht dort nicht, und im ganzen Lastenheft steht keine Zahl für die
 * Angebotsgültigkeit. Eine erfundene Frist wäre eine vertragliche Zusage. Der Admin gibt
 * sie ein, das Formular verlangt sie.
 */
final class AngebotDienst
{
    /** §4: „Stufe 0 immer 12." */
    public const MINDESTLAUFZEIT_MONATE = 12;

    private const ZAHLUNGSPLAENE = ['50_50', '40_30_30', 'custom'];

    public function __construct(
        private readonly AdminNachweis $nachweis,
        private readonly ?AdminAngebote $angebote = null,
        private readonly ?AdminProjekte $projekte = null,
        private readonly ?AuditProtokoll $audit = null,
        private readonly ?Mailversand $mail = null,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * Die Vorbelegung für ein neues Angebot — §4c.
     *
     * @return array<string,mixed>
     */
    public function vorbelegung(string $paket): array
    {
        $zeile = Preise::zeile($paket);
        $korridor = Angebotstexte::lieferkorridor($paket);

        return [
            'package'                      => $paket,
            'summary'                      => '',
            'sitemap'                      => '',
            'inclusions'                   => Angebotstexte::BFSG_ENTHALTEN,
            'exclusions'                   => Angebotstexte::BFSG_AUSGENOMMEN,
            'scope_pages'                  => null,
            'scope_words'                  => null,
            'included_feedback_rounds'     => (int) ($zeile['korrekturrunden'] ?? 0),
            'delivery_days_min'            => $korridor[0] ?? null,
            'delivery_days_max'            => $korridor[1] ?? null,
            'delivery_start_condition'     => Angebotstexte::LIEFERBEGINN,
            'one_time_net_cents'           => (int) ($zeile['einmalig_cent'] ?? 0),
            'protection_level'             => self::schutzstufe($paket),
            'protection_monthly_net_cents' => (int) ($zeile['monatlich_cent'] ?? 0),
            'protection_min_term_months'   => self::MINDESTLAUFZEIT_MONATE,
            'first_year_net_cents'         => (int) ($zeile['erstes_jahr_cent'] ?? 0),
            'payment_plan'                 => '50_50',
            'payment_plan_custom'          => null,
            'rights_text'                  => Angebotstexte::RECHTE,
            'domain_text'                  => Angebotstexte::DOMAIN,
            'bfsg_vertragsabschluss'       => 'nein',
            'bfsg_kleinstunternehmen'      => 'unbekannt',
        ];
    }

    /**
     * Legt ein Angebot als `entwurf` an.
     *
     * @param array<string,mixed> $eingabe
     *
     * @return array{fehler:list<string>,id:?string}
     */
    public function anlegen(string $projektId, array $eingabe, ?string $ip): array
    {
        $projekt = $this->projekte()->finden($projektId);

        if ($projekt === null) {
            return ['fehler' => ['Dieses Projekt gibt es nicht.'], 'id' => null];
        }

        $fehler = $this->pruefen($eingabe);

        if ($fehler !== []) {
            return ['fehler' => $fehler, 'id' => null];
        }

        $id = $this->angebote()->anlegen([
            'project_id'                   => $projektId,
            'number'                       => self::text($eingabe, 'number'),
            'status'                       => 'entwurf',
            'package'                      => self::text($eingabe, 'package'),
            'summary'                      => self::text($eingabe, 'summary'),
            'sitemap'                      => self::text($eingabe, 'sitemap'),
            'inclusions'                   => self::text($eingabe, 'inclusions'),
            'exclusions'                   => self::text($eingabe, 'exclusions'),
            'scope_pages'                  => self::zahlOderNull($eingabe, 'scope_pages'),
            'scope_words'                  => self::zahlOderNull($eingabe, 'scope_words'),
            'included_feedback_rounds'     => self::zahl($eingabe, 'included_feedback_rounds'),
            'delivery_days_min'            => self::zahl($eingabe, 'delivery_days_min'),
            'delivery_days_max'            => self::zahl($eingabe, 'delivery_days_max'),
            'delivery_start_condition'     => self::text($eingabe, 'delivery_start_condition'),
            'one_time_net_cents'           => self::zahl($eingabe, 'one_time_net_cents'),
            'protection_level'             => self::text($eingabe, 'protection_level'),
            'protection_monthly_net_cents' => self::zahl($eingabe, 'protection_monthly_net_cents'),
            'protection_min_term_months'   => self::MINDESTLAUFZEIT_MONATE,
            'first_year_net_cents'         => self::zahl($eingabe, 'first_year_net_cents'),
            'payment_plan'                 => self::text($eingabe, 'payment_plan'),
            'payment_plan_custom'          => self::text($eingabe, 'payment_plan') === 'custom'
                ? self::text($eingabe, 'payment_plan_custom')
                : null,
            'rights_text'                  => self::text($eingabe, 'rights_text'),
            'domain_text'                  => self::text($eingabe, 'domain_text'),
            'valid_until'                  => self::text($eingabe, 'valid_until'),
            'bfsg_vertragsabschluss'       => self::text($eingabe, 'bfsg_vertragsabschluss'),
            'bfsg_kleinstunternehmen'      => self::text($eingabe, 'bfsg_kleinstunternehmen'),
        ]);

        $this->audit()->schreiben(
            aktion: 'angebot_angelegt',
            objektart: 'offer',
            objektId: $id,
            akteurBenutzerId: $this->nachweis->adminBenutzerId,
            organisationId: (string) $projekt['organization_id'],
            neuerWert: 'entwurf',
            ip: $ip,
        );

        return ['fehler' => [], 'id' => $id];
    }

    /**
     * Sendet ein Angebot — §5.1a, Zeile *(Anlage)* → `angebot_offen`.
     *
     * „`offers.status = gesendet`, alle Pflichtfelder aus §4 gefüllt." Das Projekt steht
     * bereits auf `angebot_offen` (dort entsteht es), das Angebot wechselt von `entwurf`.
     *
     * @return list<string> leer bei Erfolg
     */
    public function senden(string $angebotId, ?string $ip): array
    {
        $angebot = $this->angebote()->finden($angebotId);

        if ($angebot === null) {
            return ['Dieses Angebot gibt es nicht.'];
        }

        if ((string) $angebot['status'] !== 'entwurf') {
            return ['Dieses Angebot ist bereits gesendet.'];
        }

        $fehler = $this->pruefen($angebot);

        if ($fehler !== []) {
            return $fehler;
        }

        $this->angebote()->alsGesendetVermerken($angebotId);

        $projekt = $this->projekte()->finden((string) $angebot['project_id']);

        $this->audit()->schreiben(
            aktion: 'angebot_gesendet',
            objektart: 'offer',
            objektId: $angebotId,
            akteurBenutzerId: $this->nachweis->adminBenutzerId,
            organisationId: $projekt === null ? null : (string) $projekt['organization_id'],
            alterWert: 'entwurf',
            neuerWert: 'gesendet',
            // §3: Bei Geld und Fristen ist `reason` Pflichtfeld. Ein Angebot ist beides.
            grund: 'Angebot an den Kunden gesendet',
            ip: $ip,
        );

        return [];
    }

    /**
     * Alle Prüfungen aus §4 und §4c an einer Stelle.
     *
     * @param array<string,mixed> $werte
     *
     * @return list<string>
     */
    public function pruefen(array $werte): array
    {
        $fehler = [];

        // §4: NOT NULL erlaubt ''. Für jedes Pflichtfeld zusätzlich: nach trim() mindestens
        // ein Zeichen.
        foreach ([
            'number'                   => 'Die Angebotsnummer fehlt.',
            'summary'                  => 'Die Zusammenfassung fehlt.',
            'sitemap'                  => 'Die Seitenstruktur fehlt.',
            'inclusions'               => 'Die Liste „was enthalten ist" fehlt.',
            'exclusions'               => 'Die Liste „was nicht enthalten ist" fehlt.',
            'delivery_start_condition' => 'Die Bedingung für den Lieferbeginn fehlt.',
            'rights_text'              => 'Der Text zu den Nutzungsrechten fehlt.',
            'domain_text'              => 'Der Text zur Domain fehlt.',
            'valid_until'              => 'Bitte geben Sie an, bis wann das Angebot gilt.',
        ] as $feld => $meldung) {
            if (!Validate::gefuellt(self::text($werte, $feld))) {
                $fehler[] = $meldung;
            }
        }

        // §4a: Nummernkreis AN-JJJJ-NNN.
        $nummer = self::text($werte, 'number');

        if ($nummer !== '' && preg_match('/^AN-\d{4}-\d{3,}$/', $nummer) !== 1) {
            $fehler[] = 'Die Angebotsnummer hat das Format AN-JJJJ-NNN, zum Beispiel AN-2026-001.';
        }

        // §4 Prüfregel Erstjahreswert — mit dem erwarteten Betrag in der Meldung.
        $einmalig = self::zahl($werte, 'one_time_net_cents');
        $monatlich = self::zahl($werte, 'protection_monthly_net_cents');
        $erstesJahr = self::zahl($werte, 'first_year_net_cents');
        $erwartet = $einmalig + 12 * $monatlich;

        if ($erstesJahr !== $erwartet) {
            $fehler[] = 'Der Erstjahreswert passt nicht zu Einmalpreis und Betriebspauschale. '
                . 'Erwartet: ' . \Sartu\Helpers\Format::euro($erwartet) . '. Bitte prüfen.';
        }

        // §4 Prüfregel Zahlungsplan.
        $plan = self::text($werte, 'payment_plan');
        $paket = self::text($werte, 'package');

        if (!in_array($plan, self::ZAHLUNGSPLAENE, true)) {
            $fehler[] = 'Bitte wählen Sie einen Zahlungsplan.';
        } elseif ($plan === 'custom') {
            if ($paket !== 'sonderprojekt') {
                $fehler[] = 'Ein eigener Zahlungsplan ist nur beim Sonderprojekt zulässig.';
            }

            $raten = self::text($werte, 'payment_plan_custom');

            if (!Validate::gefuellt($raten)) {
                $fehler[] = 'Bitte schreiben Sie die Raten des eigenen Zahlungsplans aus.';
            } else {
                $summe = self::ratensumme($raten);

                if ($summe === null) {
                    $fehler[] = 'Jede Rate steht in einer eigenen Zeile im Format '
                        . '„Bezeichnung | Betrag netto | Fälligkeit".';
                } elseif ($summe !== $einmalig) {
                    // Testfall 23. Ohne diese Prüfung steht im Angebot ein Gesamtpreis, den
                    // die Raten darunter nicht ergeben — und der Streit entsteht bei der
                    // letzten Rechnung.
                    $fehler[] = 'Die Raten ergeben zusammen ' . \Sartu\Helpers\Format::euro($summe)
                        . ', der Einmalpreis ist ' . \Sartu\Helpers\Format::euro($einmalig) . '. Bitte prüfen.';
                }
            }
        }

        // §4c: die Sperre. Keine Warnung.
        if (Angebotstexte::bfsgAusschluss(
            self::text($werte, 'bfsg_vertragsabschluss'),
            self::text($werte, 'bfsg_kleinstunternehmen'),
        ) === null) {
            $fehler[] = Angebotstexte::BFSG_SPERRE;
        }

        // §4c: Beide Bausteine dürfen nicht fehlen.
        if (!str_contains(self::text($werte, 'inclusions'), 'Bedienung per Tastatur')) {
            $fehler[] = 'Der Absatz zur Bedienbarkeit fehlt in „was enthalten ist". '
                . 'Er steht in jedem Angebot.';
        }

        if (!str_contains(self::text($werte, 'exclusions'), 'Barrierefreiheitsstärkungsgesetz')) {
            $fehler[] = 'Der Absatz zum Barrierefreiheitsstärkungsgesetz fehlt in '
                . '„was nicht enthalten ist". Er steht in jedem Angebot.';
        }

        $von = self::zahl($werte, 'delivery_days_min');
        $bis = self::zahl($werte, 'delivery_days_max');

        if ($von <= 0 || $bis <= 0 || $von > $bis) {
            $fehler[] = 'Der Lieferkorridor braucht zwei Werktagszahlen, die kleinere zuerst.';
        }

        return $fehler;
    }

    // ------------------------------------------------------------------ intern

    /**
     * Die Summe der Raten aus `payment_plan_custom` — Testfall 23.
     *
     * §4a gibt das Format vor: eine Rate je Zeile, `Bezeichnung | Betrag netto | Fälligkeit`,
     * Betrag deutsch geschrieben (`5.000,00 €`). Gerechnet wird in Cent, wie überall.
     *
     * @return int|null `null`, wenn eine Zeile nicht dem Format folgt — dann wird nicht
     *                  geraten, sondern abgewiesen.
     */
    public static function ratensumme(string $raten): ?int
    {
        $summe = 0;
        $zeilen = 0;

        foreach (preg_split('/\R/', $raten) ?: [] as $zeile) {
            $zeile = trim($zeile);

            if ($zeile === '') {
                continue;
            }

            $teile = array_map('trim', explode('|', $zeile));

            if (count($teile) !== 3 || $teile[0] === '' || $teile[2] === '') {
                return null;
            }

            $cent = self::centAusText($teile[1]);

            if ($cent === null) {
                return null;
            }

            $summe += $cent;
            ++$zeilen;
        }

        return $zeilen === 0 ? null : $summe;
    }

    /** `5.000,00 €` → 500000. Punkt ist Tausendertrennung, Komma die Nachkommastelle. */
    private static function centAusText(string $betrag): ?int
    {
        $roh = str_replace(['€', ' ', "\u{00A0}"], '', $betrag);

        if (preg_match('/^\d{1,3}(\.\d{3})*(,\d{2})?$|^\d+(,\d{2})?$/', $roh) !== 1) {
            return null;
        }

        $roh = str_replace('.', '', $roh);
        [$euro, $cent] = array_pad(explode(',', $roh, 2), 2, '00');

        return (int) $euro * 100 + (int) str_pad(substr($cent, 0, 2), 2, '0');
    }

    private static function schutzstufe(string $paket): string
    {
        return match ($paket) {
            'start'    => 's',
            'wachstum' => 'm',
            default    => 'l',
        };
    }

    /** @param array<string,mixed> $werte */
    private static function text(array $werte, string $feld): string
    {
        $wert = $werte[$feld] ?? '';

        return is_scalar($wert) ? trim((string) $wert) : '';
    }

    /** @param array<string,mixed> $werte */
    private static function zahl(array $werte, string $feld): int
    {
        $wert = $werte[$feld] ?? 0;

        return is_numeric($wert) ? (int) $wert : 0;
    }

    /** @param array<string,mixed> $werte */
    private static function zahlOderNull(array $werte, string $feld): ?int
    {
        $wert = $werte[$feld] ?? null;

        return is_numeric($wert) ? (int) $wert : null;
    }

    private function angebote(): AdminAngebote
    {
        return $this->angebote ?? new AdminAngebote($this->nachweis, $this->pdo);
    }

    private function projekte(): AdminProjekte
    {
        return $this->projekte ?? new AdminProjekte($this->nachweis, $this->pdo);
    }

    private function audit(): AuditProtokoll
    {
        return $this->audit ?? new AuditProtokoll($this->pdo);
    }
}
