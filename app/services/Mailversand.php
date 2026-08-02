<?php

declare(strict_types=1);

namespace Sartu\Services;

use PHPMailer\PHPMailer\Exception as MailFehler;
use PHPMailer\PHPMailer\PHPMailer;
use Sartu\Helpers\Env;

/**
 * Mailversand ueber SMTP — Portal-Lastenheft §10.
 *
 * Alle Mails gehen als Klartext UND einfaches HTML raus. Lokal faengt Mailpit alles ab,
 * Posteingang unter http://localhost:8025.
 *
 * Die Ersteinrichtung nutzt dieselbe Klasse (§1.5 Schritt 5) — mit den gerade eingegebenen
 * Zugangsdaten, bevor sie in der .env stehen. Deshalb sind sie Parameter und keine
 * Konfigurationsabfrage im Innern.
 */
final class Mailversand
{
    public function __construct(
        private readonly ?SmtpZugang $zugang = null,
    ) {
    }

    public function senden(string $an, string $betreff, string $klartext, ?string $html = null): void
    {
        $zugang = $this->zugang ?? SmtpZugang::ausKonfiguration();

        $mailer = new PHPMailer(true);

        try {
            $mailer->isSMTP();
            $mailer->Host = $zugang->host;
            $mailer->Port = $zugang->port;
            $mailer->CharSet = PHPMailer::CHARSET_UTF8;
            $mailer->Timeout = 15;

            if ($zugang->benutzer !== '') {
                $mailer->SMTPAuth = true;
                $mailer->Username = $zugang->benutzer;
                $mailer->Password = $zugang->passwort;
            } else {
                // Mailpit nimmt lokal ohne Anmeldung und ohne TLS an.
                $mailer->SMTPAuth = false;
                $mailer->SMTPAutoTLS = false;
            }

            $mailer->setFrom($zugang->absender, $zugang->absenderName);
            $mailer->addAddress($an);
            $mailer->Subject = $betreff;

            if ($html === null) {
                $mailer->Body = $klartext;
            } else {
                $mailer->isHTML(true);
                $mailer->Body = $html;
                $mailer->AltBody = $klartext;
            }

            $mailer->send();
        } catch (MailFehler $fehler) {
            // Die Meldung von PHPMailer nennt Host und Port, aber nie das Passwort.
            // §1.5: Zugangsdaten werden nie in eine Fehlermeldung geschrieben.
            throw new MailversandFehler($fehler->getMessage(), previous: $fehler);
        }
    }
}
