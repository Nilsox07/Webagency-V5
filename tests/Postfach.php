<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Services\MailversandFehler;
use Sartu\Services\Versender;

/**
 * Ein Mailversand, der nichts verschickt, sondern mitschreibt.
 *
 * **Warum das nötig ist:** Bis zum 02.08.2026 prüfte kein Test, ob eine Mail rausging — die
 * Dienste riefen den Versand auf, und der lief lokal gegen Mailpit oder schlug still fehl.
 * §4b.6 macht die Mail beim Kontaktformular zum **einzigen** Träger der Rückfrage. Ob sie
 * rausgeht und was drinsteht, ist damit prüfbar und muss geprüft werden.
 *
 * `scheitert: true` wirft denselben Fehler wie ein nicht erreichbarer SMTP-Server. Nur so
 * lässt sich zeigen, dass der Absender einen Fehlschlag **erfährt** — und nicht eine
 * Bestätigung für eine Nachricht bekommt, die nirgends angekommen ist.
 *
 * **Dies ist ein Testmittel, kein zweiter Weg nach draussen.** `Mailversand` bleibt die
 * einzige Umsetzung von `Versender` im Anwendungscode und bleibt `final`.
 */
final class Postfach implements Versender
{
    /** @var list<array{an:string,betreff:string,text:string}> */
    public array $mails = [];

    public function __construct(private readonly bool $scheitert = false)
    {
    }

    public function senden(string $an, string $betreff, string $klartext, ?string $html = null): void
    {
        if ($this->scheitert) {
            throw new MailversandFehler('Der Mailserver ist im Test absichtlich nicht erreichbar.');
        }

        $this->mails[] = ['an' => $an, 'betreff' => $betreff, 'text' => $klartext];
    }
}
