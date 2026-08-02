<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Helpers\Env;

/**
 * „Zusätzlich: Benachrichtigungs-E-Mail an SARTU" — Website-Lastenheft §9.5b,
 * Portal-Lastenheft §10, Zeile „Neue Anfrage über die Website (an Admin)".
 *
 * ## Eine interne Kurzmeldung, kein Datenauszug
 *
 * §10 nennt den Inhalt: empfohlener Umfang, Ampelkennzeichen, Link auf `/admin/anfragen`.
 * Mehr steht nicht drin — weder Antworttexte noch die Telefonnummer. Wer die Anfrage
 * ansehen will, meldet sich an; dort greift die Zugriffsprüfung. Eine Mail greift nirgends.
 *
 * ## Der Empfänger ist eine Lücke, die nicht erfunden wird
 *
 * `ADMIN_NOTIFY_EMAIL` steht in Portal-Lastenheft §1.5 unter „Erforderliche Werte", wird
 * aber in **keinem** der acht Einrichtungsschritte erhoben — gemeldet in
 * `OFFENE_PRUEFUNGEN.md`, seit A0. Ist der Wert leer, wird **nicht** ersatzweise an die
 * Impressumsadresse geschickt: Das wäre eine erfundene Festlegung.
 *
 * Stattdessen: nichts senden, `false` zurückgeben, Anfrage bleibt gespeichert.
 *
 * ## Eine gescheiterte Mail darf die Anfrage nie kosten
 *
 * Der `lead` steht zu diesem Zeitpunkt bereits in der Datenbank. Fällt der Mailserver aus,
 * sieht der Interessent trotzdem die Danke-Seite — sonst schickt er die Anfrage ein zweites
 * Mal, und SARTU verliert einen Interessenten an einen SMTP-Fehler.
 */
final class Anfragebenachrichtigung
{
    private const AMPEL = [
        'standard' => 'Standard — keine Prüfung nötig',
        'gelb'     => 'Gelb — eine Rückfrage',
        'orange'   => 'Orange — Fachmodul vor dem Angebot',
        'rot'      => 'Rot — Sonderprojekt',
    ];

    public function __construct(private readonly ?Mailversand $versand = null)
    {
    }

    /** @return bool `false`, wenn kein Empfänger hinterlegt ist oder der Versand scheitert. */
    public function senden(string $unternehmen, string $paket, string $ampel): bool
    {
        $empfaenger = trim((string) Env::get('ADMIN_NOTIFY_EMAIL', ''));

        if ($empfaenger === '') {
            return false;
        }

        try {
            ($this->versand ?? new Mailversand())->senden(
                $empfaenger,
                self::betreff($unternehmen),
                self::nachricht($unternehmen, $paket, $ampel),
            );
        } catch (MailversandFehler) {
            // Kein erneutes Werfen: Der Datensatz ist sicher, und der Interessent hat mit
            // dem Mailserver nichts zu tun.
            return false;
        }

        return true;
    }

    /** §10: `Neue Anfrage: {Unternehmen}` — der Wortlaut steht dort gebunden. */
    public static function betreff(string $unternehmen): string
    {
        return 'Neue Anfrage: ' . $unternehmen;
    }

    /**
     * Der vollständige Text.
     *
     * Öffentlich, damit ein Test ihn lesen kann, ohne einen Mailserver zu brauchen — und
     * damit prüfbar bleibt, dass **nichts** darin steht, was §10 nicht nennt.
     */
    public static function nachricht(string $unternehmen, string $paket, string $ampel): string
    {
        return "Guten Tag,\n\n"
            . "über den Bedarfsscheck ist eine neue Anfrage eingegangen.\n\n"
            . 'Unternehmen: ' . $unternehmen . "\n"
            . 'Empfohlener Umfang: ' . Preise::name($paket) . "\n"
            . 'Kennzeichen: ' . (self::AMPEL[$ampel] ?? $ampel) . "\n\n"
            . 'Die vollständige Anfrage steht unter ' . self::adresse() . "/admin/anfragen\n\n"
            . "Freundliche Grüße\nSARTU\n";
    }

    private static function adresse(): string
    {
        return rtrim((string) Env::get('BASE_URL', ''), '/');
    }
}
