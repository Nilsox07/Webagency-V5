<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Services\Zahlungsdienst;
use Sartu\Services\ZahlungsdienstFehler;

/**
 * Ein Zahlungsdienst, der nicht ins Netz geht, sondern antwortet, was ihm mitgegeben wurde.
 *
 * **Warum das nicht dasselbe ist wie „den Abruf überspringen".** Der Kern von Fall 90 ist,
 * dass der Server den Zustand **holt** und ihn nicht entgegennimmt. Eine Attrappe, die auf
 * `zahlungLesen()` antwortet, prüft genau diesen Weg: Was hier hinterlegt ist, kennt der
 * Aufrufer des Webhooks nicht und kann es nicht beeinflussen. Der Test kann deshalb einen
 * Webhook mit erfundenen Angaben schicken und zeigen, dass **die hinterlegte** Antwort gilt.
 *
 * `zaehler` hält fest, wie oft abgerufen wurde — Fall 91 braucht die Zahl, nicht nur den
 * Zustand am Ende.
 */
final class Zahlungsstelle implements Zahlungsdienst
{
    /** @var array<string,array{kennung:string,zustand:string,betrag_cents:int,waehrung:string,bezahlt_am:?string,referenz:?string}> */
    private array $zahlungen = [];

    public int $abrufe = 0;

    public int $anlagen = 0;

    /** @var list<array{betrag:int,waehrung:string,referenz:string,rueckkehr:string,webhook:string}> */
    public array $angelegt = [];

    public function __construct(private readonly bool $scheitert = false)
    {
    }

    public function hinterlegen(
        string $kennung,
        string $zustand,
        int $betragCent,
        string $waehrung = 'EUR',
        ?string $referenz = null,
    ): void {
        $this->zahlungen[$kennung] = [
            'kennung'      => $kennung,
            'zustand'      => $zustand,
            'betrag_cents' => $betragCent,
            'waehrung'     => $waehrung,
            'bezahlt_am'   => $zustand === 'paid' ? '2026-08-09T10:00:00+00:00' : null,
            'referenz'     => $referenz,
        ];
    }

    public function zahlungAnlegen(
        int $betragCent,
        string $waehrung,
        string $referenz,
        string $rueckkehr,
        string $webhook,
    ): array {
        if ($this->scheitert) {
            throw new ZahlungsdienstFehler('Der Zahlungsdienst ist im Test absichtlich nicht erreichbar.');
        }

        ++$this->anlagen;

        $kennung = 'tr_test' . str_pad((string) $this->anlagen, 4, '0', STR_PAD_LEFT);

        $this->angelegt[] = compact('waehrung', 'referenz', 'rueckkehr', 'webhook') + ['betrag' => $betragCent];
        $this->hinterlegen($kennung, 'open', $betragCent, $waehrung, $referenz);

        return ['kennung' => $kennung, 'adresse' => 'https://www.mollie.com/checkout/' . $kennung];
    }

    public function zahlungLesen(string $kennung): array
    {
        if ($this->scheitert) {
            throw new ZahlungsdienstFehler('Der Zahlungsdienst ist im Test absichtlich nicht erreichbar.');
        }

        ++$this->abrufe;

        if (!isset($this->zahlungen[$kennung])) {
            throw new ZahlungsdienstFehler('Diese Zahlung gibt es beim Dienst nicht.');
        }

        return $this->zahlungen[$kennung];
    }
}
