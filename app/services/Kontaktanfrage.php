<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\AnfrageSpeicher;
use Sartu\Helpers\Validate;

/**
 * Das Rückfrageformular auf `/kontakt` — Website-Lastenheft §11.
 *
 * ## Warum eine Rückfrage in `leads` landet
 *
 * **Für das Kontaktformular gibt es keine eigene Tabelle im Datenmodell.** Portal-Lastenheft
 * §4 kennt `leads` für den Bedarfsscheck und `support_messages` für Nachrichten
 * **angemeldeter Kunden**. Wer über `/kontakt` schreibt, ist beides nicht.
 *
 * Geprüft nach der Regel aus dem Auftrag §6: Nennt eine andere Passage die Tabelle? Nein.
 * Also wird sie nicht erfunden. `leads` ist die vorhandene Anfragetabelle, sie trägt
 * Absender, Herkunft, Löschfrist und Freitext in `payload` — eine Rückfrage passt hinein,
 * ohne dass ein Feld seine Bedeutung ändert. `recommended_package` bleibt `NULL`: Es gab
 * keinen Bedarfsscheck, also gibt es keine Empfehlung.
 *
 * ## Die B2B-Bestätigung steht im Formular, obwohl §11 sie nicht aufzählt
 *
 * §11 nennt sieben Felder und darunter nur die Datenschutz-Bestätigung. Die
 * Prüfbedingung `chk_leads_bestaetigungen` verlangt **beide**.
 *
 * Zwei Wege standen offen, und einer davon war keiner:
 *
 * | Weg | Folge |
 * |---|---|
 * | `b2b_confirmed = 1` schreiben, ohne zu fragen | Eine Erklärung fälschen, die der Absender nie abgegeben hat |
 * | Das Feld ins Formular aufnehmen | Eine Abweichung von §11, die im Bericht steht |
 *
 * Der zweite Weg ist gewählt. Website-Lastenheft §2 macht die Beschränkung auf Unternehmer
 * ohnehin zur harten Regel — „Ausschließlich für Unternehmer" steht unter jeder Preisnennung.
 * Ein Formular, das das nicht abfragt, widerspricht der Seite, auf der es steht.
 *
 * ## Dieselbe Abwehr wie beim Bedarfsscheck
 *
 * §17 verlangt sie für **beide** Formulare. Sie steht in `Formularschutz`, einmal.
 */
final class Kontaktanfrage
{
    /** §11 — die vier Auswahlmöglichkeiten unter „Anliegen". */
    public const ANLIEGEN = [
        'websiteprojekt' => 'Websiteprojekt',
        'angebot'        => 'Bestehendes Angebot',
        'domain'         => 'Domain und Launch',
        'rueckfrage'     => 'Allgemeine Rückfrage',
    ];

    /** §11: „Nachricht (Pflicht, min. 20 Zeichen)". */
    public const NACHRICHT_MINDESTLAENGE = 20;

    public const BESTAETIGUNG = 'Danke — Ihre Nachricht ist angekommen. Wir antworten '
        . 'schriftlich, in der Regel innerhalb eines Werktags.';

    public function __construct(
        private readonly ?AnfrageSpeicher $speicher = null,
        private readonly ?Ratenbegrenzung $begrenzung = null,
        private readonly ?Projektmail $mail = null,
    ) {
    }

    /**
     * @param array<string,mixed> $eingabe
     * @param array<string,string|null> $herkunft aus `Herkunft::ausSitzung()`
     */
    public function anlegen(array $eingabe, array $herkunft = [], ?string $ip = null): AnfrageErgebnis
    {
        if (Formularschutz::zuGross($eingabe)) {
            return AnfrageErgebnis::abgewiesen(['Ihre Nachricht ist zu lang. Bitte kürzen Sie sie.']);
        }

        $schluessel = 'kontakt-ip:' . (string) $ip;

        if (!$this->begrenzung()->erlaubt($schluessel, AnfrageService::VERSUCHE_JE_IP, Formularschutz::FENSTER_SEKUNDEN)) {
            return AnfrageErgebnis::begrenzt();
        }

        // Still — der Absender sieht die Bestätigungsseite, nicht den Grund.
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

        if ($this->speicher()->kenntEinreichung($submissionId)) {
            return AnfrageErgebnis::stillVerworfen();
        }

        $fehler = $this->pflichtfelderPruefen($eingabe);

        if ($fehler !== []) {
            return AnfrageErgebnis::abgewiesenAmFeld($fehler);
        }

        $this->begrenzung()->vermerken($schluessel, Formularschutz::FENSTER_SEKUNDEN);

        $jetzt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $anliegen = self::text($eingabe, 'anliegen');
        $name = self::text($eingabe, 'name');

        $id = $this->speicher()->anlegen([
            'submission_id' => $submissionId,
            'submitted_at'  => $jetzt->format('Y-m-d H:i:s'),
            'payload'       => json_encode([
                'formular' => 'kontakt',
                'anliegen' => $anliegen,
                'nachricht' => self::text($eingabe, 'nachricht'),
            ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            // §11 fragt einen Namen ab, nicht Vor- und Nachname getrennt. `leads` verlangt
            // beide Spalten — der ganze Name steht deshalb im Vornamensfeld, und der
            // Nachname bleibt leer. Ihn zu raten, indem man am letzten Leerzeichen trennt,
            // geht bei „van der Berg" und bei „Dr. Meier" schief.
            'first_name'          => $name,
            'last_name'           => '',
            'company'             => self::text($eingabe, 'company'),
            'email'               => mb_strtolower(self::text($eingabe, 'email')),
            'phone'               => self::textOderNull($eingabe, 'phone'),
            'preferred_contact'   => 'email',
            'recommended_package' => null,
            'flag'                => 'standard',
            'status'              => 'neu',
            'b2b_confirmed'       => 1,
            'privacy_confirmed'   => 1,
            'source_ip'           => $ip,
            'delete_after'        => $jetzt->modify('+' . AnfrageService::FRIST_MONATE_OFFEN . ' months')
                ->format('Y-m-d'),
            ...self::herkunftsfelder($herkunft),
        ]);

        $this->betreuerBenachrichtigen($name, $anliegen);

        return AnfrageErgebnis::angelegt($id, null, 'standard', []);
    }

    // ------------------------------------------------------------------ intern

    /** @return array<string,string> je Feldname eine Meldung — §11 nennt die Nachrichtenmeldung wörtlich. */
    private function pflichtfelderPruefen(array $eingabe): array
    {
        $fehler = [];

        if (!Validate::gefuellt(self::text($eingabe, 'name'))) {
            $fehler['name'] = 'Bitte geben Sie Ihren Namen an.';
        }

        if (!Validate::gefuellt(self::text($eingabe, 'company'))) {
            $fehler['company'] = 'Bitte geben Sie Ihr Unternehmen an.';
        }

        if (!Validate::email(self::text($eingabe, 'email'))) {
            $fehler['email'] = 'Bitte geben Sie eine gültige E-Mail-Adresse an, z. B. name@firma.de';
        }

        if (!isset(self::ANLIEGEN[self::text($eingabe, 'anliegen')])) {
            $fehler['anliegen'] = 'Bitte wählen Sie, worum es geht.';
        }

        if (mb_strlen(self::text($eingabe, 'nachricht')) < self::NACHRICHT_MINDESTLAENGE) {
            // §11, Wortlaut gebunden.
            $fehler['nachricht'] = 'Bitte beschreiben Sie Ihr Anliegen in ein bis zwei Sätzen.';
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
     * Die interne Kurzmeldung — wie beim Bedarfsscheck: **kein Datenauszug**.
     *
     * Weder Nachricht noch E-Mail-Adresse stehen darin. Wer die Anfrage lesen will, meldet
     * sich an; dort greift die Zugriffsprüfung. Eine Mail greift nirgends.
     */
    private function betreuerBenachrichtigen(string $name, string $anliegen): void
    {
        ($this->mail ?? new Projektmail())->anBetreuer(
            ['title' => ''],
            'Neue Rückfrage über die Website',
            'Über das Kontaktformular ist eine Rückfrage eingegangen.' . "\n\n"
            . 'Absender: ' . $name . "\n"
            . 'Anliegen: ' . (self::ANLIEGEN[$anliegen] ?? $anliegen) . "\n\n"
            . 'Die vollständige Anfrage steht unter /admin/anfragen' . "\n",
        );
    }

    /** @return array<string,string|null> */
    private static function herkunftsfelder(array $herkunft): array
    {
        $felder = [];

        foreach (['landing_page', 'referrer_host', 'utm_source', 'utm_medium', 'utm_campaign',
                  'utm_term', 'utm_content', 'click_id'] as $feld) {
            $wert = $herkunft[$feld] ?? null;
            $felder[$feld] = is_string($wert) && $wert !== '' ? $wert : null;
        }

        return $felder;
    }

    private static function text(array $eingabe, string $feld): string
    {
        $wert = $eingabe[$feld] ?? null;

        return is_string($wert) ? trim($wert) : '';
    }

    private static function textOderNull(array $eingabe, string $feld): ?string
    {
        $wert = self::text($eingabe, $feld);

        return $wert === '' ? null : $wert;
    }

    private static function wahr(array $eingabe, string $feld): bool
    {
        return in_array($eingabe[$feld] ?? null, ['1', 'ja', 'on', true], true);
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
