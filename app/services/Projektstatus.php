<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Die Übergangstabelle aus Portal-Lastenheft §5.1a.
 *
 * > „Eine Liste von Zuständen ohne Übergangsregeln ist keine Statuslogik. Ohne diese Tabelle
 * > wird sie beim Bauen erfunden — und zwar an der teuersten Stelle: Produktion startet vor
 * > Zahlungseingang, oder der Lieferkorridor beginnt zu früh."
 *
 * **Was hier nicht steht, ist nicht erlaubt.** Die Tabelle ist die vollständige Quelle; ein
 * nicht aufgeführtes Paar wird abgewiesen, nicht ausgeführt. Die Prüfbedingung auf
 * `projects.status` fängt nur den erfundenen Zustand ab — welches *Paar* erlaubt ist, weiß
 * allein diese Klasse.
 *
 * ## Drei Eigenschaften, die keine Bequemlichkeit sind
 *
 * **`wer`** hält fest, wer den Wechsel auslöst.
 *
 * **`erklaerung`** markiert die drei, die §5.1a am Ende so nennt: „Kundenausgelöste Wechsel
 * sind genau drei: Angebotsannahme, Faktenfreigabe, Abnahme. **Alle drei sind Erklärungen
 * mit Namen und Zeitpunkt.**"
 *
 * *Zwei Stellen in §5.1a, die sich auf den ersten Blick widersprechen:* Die Tabelle nennt
 * **vier** Zeilen mit „Kunde" — die drei oben plus `vorschau → korrektur`, „Rückmeldungen
 * abgeschickt". Der Satz danach sagt „genau drei".
 *
 * Aufgelöst über die Begründung, die der Satz mitliefert: Er zählt nicht, wer klickt,
 * sondern welche Wechsel **Erklärungen mit Namen und Zeitpunkt** sind. Rückmeldungen
 * einreichen ist eine Handlung, keine Erklärung — es wird kein Name getippt und nichts
 * bestätigt. Beide Stellen bleiben damit gültig, und keine wird verworfen: `wer` sagt, wer
 * handelt, `erklaerung` sagt, was rechtlich zählt.
 *
 * **`grundPflicht`** markiert die Wechsel, bei denen `audit_events.reason` Pflichtfeld ist:
 * Geld und Fristen (§4, §12). Ohne diese Spalte müsste jeder Aufrufer sie kennen.
 *
 * **`pausiert`** ist der einzige Zustand mit gespeichertem Herkunftsstatus. Die Rückkehr
 * geht auf genau diesen Wert, nie auf einen frei gewählten — sonst liesse sich der Zielstatus
 * über die Pause setzen.
 */
final class Projektstatus
{
    public const ANGEBOT_OFFEN        = 'angebot_offen';
    public const ANGEBOT_ANGENOMMEN   = 'angebot_angenommen';
    public const ZAHLUNG_OFFEN        = 'zahlung_offen';
    public const BRIEFING             = 'briefing';
    public const PRODUKTION           = 'produktion';
    public const VORSCHAU             = 'vorschau';
    public const KORREKTUR            = 'korrektur';
    public const ABNAHME              = 'abnahme';
    public const LAUNCH_VORBEREITUNG  = 'launch_vorbereitung';
    public const LIVE                 = 'live';
    public const PAUSIERT             = 'pausiert';

    public const KUNDE = 'kunde';
    public const ADMIN = 'admin';

    /**
     * §5.1a, Zeile für Zeile. Reihenfolge wie im Lastenheft.
     *
     * @return list<array{von:string,nach:string,ereignis:string,wer:string,grundPflicht:bool,erklaerung:bool}>
     */
    public static function uebergaenge(): array
    {
        return [
            ['von' => self::ANGEBOT_OFFEN, 'nach' => self::ANGEBOT_ANGENOMMEN,
             'ereignis' => 'Angebot angenommen', 'wer' => self::KUNDE, 'grundPflicht' => false, 'erklaerung' => true],
            ['von' => self::ANGEBOT_ANGENOMMEN, 'nach' => self::ZAHLUNG_OFFEN,
             'ereignis' => 'Anzahlungsrechnung gesendet', 'wer' => self::ADMIN, 'grundPflicht' => true, 'erklaerung' => false],
            ['von' => self::ZAHLUNG_OFFEN, 'nach' => self::BRIEFING,
             'ereignis' => 'Zahlungseingang bestätigt', 'wer' => self::ADMIN, 'grundPflicht' => true, 'erklaerung' => false],
            ['von' => self::BRIEFING, 'nach' => self::PRODUKTION,
             'ereignis' => 'Faktenfreigabe erteilt', 'wer' => self::KUNDE, 'grundPflicht' => true, 'erklaerung' => true],
            ['von' => self::PRODUKTION, 'nach' => self::VORSCHAU,
             'ereignis' => 'Vorschau veröffentlicht', 'wer' => self::ADMIN, 'grundPflicht' => false, 'erklaerung' => false],
            ['von' => self::VORSCHAU, 'nach' => self::KORREKTUR,
             'ereignis' => 'Rückmeldungen abgeschickt', 'wer' => self::KUNDE, 'grundPflicht' => false, 'erklaerung' => false],
            ['von' => self::KORREKTUR, 'nach' => self::VORSCHAU,
             'ereignis' => 'überarbeitete Vorschau bereit', 'wer' => self::ADMIN, 'grundPflicht' => false, 'erklaerung' => false],
            ['von' => self::VORSCHAU, 'nach' => self::ABNAHME,
             'ereignis' => 'keine weiteren Änderungen', 'wer' => self::ADMIN, 'grundPflicht' => false, 'erklaerung' => false],
            ['von' => self::ABNAHME, 'nach' => self::LAUNCH_VORBEREITUNG,
             'ereignis' => 'Abnahme erklärt', 'wer' => self::KUNDE, 'grundPflicht' => true, 'erklaerung' => true],
            ['von' => self::ABNAHME, 'nach' => self::KORREKTUR,
             'ereignis' => 'Rücksprung', 'wer' => self::ADMIN, 'grundPflicht' => true, 'erklaerung' => false],
            ['von' => self::LAUNCH_VORBEREITUNG, 'nach' => self::LIVE,
             'ereignis' => 'Onlinegang', 'wer' => self::ADMIN, 'grundPflicht' => true, 'erklaerung' => false],
        ];
    }

    /**
     * Der Anlagezustand.
     *
     * §5.1a: „*(Anlage)* → `angebot_offen`, Angebot gesendet, Admin." Ein Projekt entsteht
     * **mit** dem gesendeten Angebot — es gibt keinen Zustand davor. Deshalb ist das kein
     * Übergang, sondern die Anlage selbst.
     */
    public const ANLAGE = self::ANGEBOT_OFFEN;

    /** @return array{von:string,nach:string,ereignis:string,wer:string,grundPflicht:bool,erklaerung:bool}|null */
    public static function uebergang(string $von, string $nach): ?array
    {
        // Die Pause steht in §5.1a als zwei Sammelzeilen und nicht als Paarliste. Sie hier
        // auszuschreiben hiesse, zehn Zeilen zu erfinden, die dort nicht stehen.
        if ($nach === self::PAUSIERT) {
            return $von === self::LIVE || $von === self::PAUSIERT
                ? null
                : ['von' => $von, 'nach' => self::PAUSIERT, 'ereignis' => 'Projekt angehalten',
                   'wer' => self::ADMIN, 'grundPflicht' => true, 'erklaerung' => false];
        }

        if ($von === self::PAUSIERT) {
            // Zurück geht es ausschliesslich auf den gespeicherten Herkunftsstatus. Ob der
            // übergebene Zielwert dieser ist, prüft der Aufrufer gegen `paused_from_status`
            // — hier steht nur, dass die Fortsetzung dem Admin gehört.
            return ['von' => self::PAUSIERT, 'nach' => $nach, 'ereignis' => 'Fortsetzung',
                    'wer' => self::ADMIN, 'grundPflicht' => false, 'erklaerung' => false];
        }

        foreach (self::uebergaenge() as $eintrag) {
            if ($eintrag['von'] === $von && $eintrag['nach'] === $nach) {
                return $eintrag;
            }
        }

        return null;
    }

    public static function erlaubt(string $von, string $nach): bool
    {
        return self::uebergang($von, $nach) !== null;
    }

    public static function darfKundeAusloesen(string $von, string $nach): bool
    {
        return (self::uebergang($von, $nach)['wer'] ?? null) === self::KUNDE;
    }

    /**
     * §5.1a: „Kundenausgelöste Wechsel sind genau drei […] Erklärungen mit Namen und
     * Zeitpunkt." Diese drei brauchen den selbst getippten Namen und den Zeitpunkt als
     * Nachweis — die vierte Kundenhandlung (`vorschau → korrektur`) nicht.
     *
     * @return list<array<string,mixed>>
     */
    public static function erklaerungen(): array
    {
        return array_values(array_filter(
            self::uebergaenge(),
            static fn (array $u) => $u['erklaerung'],
        ));
    }

    /**
     * Der Kundentext eines Zustands — §3 Regel 12: nie ein Systemcode.
     *
     * Die Texte stammen aus §5.1 und §8.1; sie beschreiben, was gerade passiert, nicht wie
     * die Spalte heisst.
     */
    public static function kundentext(string $status): string
    {
        return match ($status) {
            self::ANGEBOT_OFFEN       => 'Ihr Angebot liegt bereit',
            self::ANGEBOT_ANGENOMMEN  => 'Angebot angenommen',
            self::ZAHLUNG_OFFEN       => 'Rechnung offen',
            self::BRIEFING            => 'Wir brauchen Angaben von Ihnen',
            self::PRODUKTION          => 'Wir bauen Ihre Website',
            self::VORSCHAU            => 'Ihre Vorschau ist bereit',
            self::KORREKTUR           => 'Wir arbeiten Ihre Rückmeldungen ein',
            self::ABNAHME             => 'Ihre Abnahme fehlt noch',
            self::LAUNCH_VORBEREITUNG => 'Wir bereiten den Onlinegang vor',
            self::LIVE                => 'Ihre Website ist online',
            self::PAUSIERT            => 'Ihr Projekt pausiert',
            default                   => 'Ihr Projekt läuft',
        };
    }

    /**
     * Die sieben Stationen der Fortschrittsanzeige — §8.1, verbindliche Zuordnung.
     *
     * Der Kasten unter der Tabelle nennt die vier Fälle, die vorher nicht bestimmt waren:
     * `korrektur` gehört zu **Produktion**, nicht zu Vorschau — aus Sicht des Kunden wird
     * gearbeitet, nicht angesehen. `launch_vorbereitung` gehört zu **Abnahme**, nicht zu
     * Online — online ist die Seite erst, wenn sie erreichbar ist.
     */
    public const STATIONEN = ['Angebot', 'Zahlung', 'Angaben', 'Produktion', 'Vorschau', 'Abnahme', 'Online'];

    /** @return string|null `null` bei `pausiert` — dort wird KEINE Station markiert (§8.1). */
    public static function station(string $status): ?string
    {
        return match ($status) {
            self::ANGEBOT_OFFEN, self::ANGEBOT_ANGENOMMEN  => 'Angebot',
            self::ZAHLUNG_OFFEN                            => 'Zahlung',
            self::BRIEFING                                 => 'Angaben',
            self::PRODUKTION, self::KORREKTUR              => 'Produktion',
            self::VORSCHAU                                 => 'Vorschau',
            self::ABNAHME, self::LAUNCH_VORBEREITUNG       => 'Abnahme',
            self::LIVE                                     => 'Online',
            default                                        => null,
        };
    }
}
