<?php

declare(strict_types=1);

namespace Sartu\Admin;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\AuditProtokoll;
use Sartu\Helpers\Http;
use Sartu\Helpers\Validate;
use Sartu\Services\Mailversand;
use Sartu\Services\MailversandFehler;

/**
 * Testmailversand aus dem internen Bereich — eine der sechs Funktionen von A0.
 *
 * Die Ersteinrichtung prueft den Versand einmal. Danach aendern sich Zugangsdaten,
 * DNS-Eintraege und Anbieter — deshalb bleibt die Pruefung als eigener Punkt erreichbar.
 */
final class TestmailSteuerung
{
    /**
     * @param array<string,string> $parameter
     * @param list<string> $fehler
     * @param list<string> $hinweise
     */
    public function formular(array $parameter = [], array $fehler = [], array $hinweise = []): Antwort
    {
        return Antwort::html(Ansicht::seite('admin', 'admin-testmail', [
            'titel'      => 'Testnachricht senden',
            'angemeldet' => true,
            'fehler'     => $fehler,
            'hinweise'   => $hinweise,
        ]));
    }

    /** @param array<string,string> $parameter */
    public function senden(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $an = Http::getrimmteEingabe('an');

        if (!Validate::email($an)) {
            return $this->formular([], ['Die E-Mail-Adresse ist nicht vollständig.']);
        }

        try {
            (new Mailversand())->senden(
                $an,
                'Testnachricht aus Ihrem Bereich',
                "Guten Tag,\n\nder Mailversand funktioniert. Diese Nachricht wurde aus Ihrem Bereich "
                . "verschickt.\n\nFreundliche Grüße\nSARTU\n",
            );
        } catch (MailversandFehler $fehler) {
            return $this->formular([], ['Die Nachricht ging nicht raus: ' . $fehler->getMessage()]);
        }

        (new AuditProtokoll())->schreiben(
            aktion: 'testmail_gesendet',
            objektart: 'mail',
            akteurBenutzerId: $nachweis->adminBenutzerId,
            neuerWert: $an,
            ip: Http::gegenstelle(),
        );

        return $this->formular([], [], ['Die Nachricht ist raus. Sehen Sie im Posteingang nach, auch im Spam-Ordner.']);
    }
}
