<?php

declare(strict_types=1);

namespace Sartu\Admin;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Helpers\Http;
use Sartu\Services\AnmeldeDienst;
use Sartu\Sitzung;

/**
 * Anmeldung und Abmeldung des internen Bereichs.
 *
 * Die Fehlermeldung ist in beiden Faellen dieselbe. Welcher Teil nicht stimmte — Adresse,
 * Passwort oder Code —, steht nirgends: Sonst waere die Seite eine Auskunft darueber,
 * welche Adressen es gibt.
 */
final class AnmeldeSteuerung
{
    private const MELDUNG = 'E-Mail-Adresse oder Passwort stimmt nicht.';

    private const MELDUNG_CODE = 'Der Code stimmt nicht. Er wechselt alle 30 Sekunden.';

    public function __construct(private readonly ?AnmeldeDienst $dienst = null)
    {
    }

    /** @param array<string,string> $parameter */
    public function formular(array $parameter = [], array $fehler = []): Antwort
    {
        if (Sitzung::istAngemeldeterAdmin()) {
            return Antwort::weiter('/admin');
        }

        $betreiber = $this->betreiberdaten();

        return Antwort::html(Ansicht::seite('oeffentlich', 'anmelden', [
            'titel'   => 'Anmeldung',
            'fehler'  => $fehler,
            // Testfall 83: Die Nummer kommt aus den Betreiberdaten, nie aus dem Quelltext.
            // Fehlt sie, erscheint die E-Mail-Adresse.
            'telefon' => $this->wert($betreiber, 'telefon'),
            'email'   => $this->wert($betreiber, 'email'),
        ]));
    }

    /** @param array<string,string> $parameter */
    public function anmelden(array $parameter = []): Antwort
    {
        $email = Http::getrimmteEingabe('email');
        $ip = Http::gegenstelle();

        if ($this->dienst()->gesperrt($email, $ip)) {
            return $this->formular([], ['Zu viele Versuche. Bitte warten Sie eine Stunde.']);
        }

        if (!$this->dienst()->passwortPruefen($email, Http::eingabe('passwort') ?? '', $ip)) {
            return $this->formular([], [self::MELDUNG]);
        }

        return Antwort::weiter('/admin/anmelden/code');
    }

    /** @param array<string,string> $parameter */
    public function codeFormular(array $parameter = [], array $fehler = []): Antwort
    {
        if ($this->dienst()->vorgemerkterBenutzer() === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        return Antwort::html(Ansicht::seite('oeffentlich', 'anmelden-code', [
            'titel'  => 'Zweiter Faktor',
            'fehler' => $fehler,
        ]));
    }

    /** @param array<string,string> $parameter */
    public function code(array $parameter = []): Antwort
    {
        if ($this->dienst()->vorgemerkterBenutzer() === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $token = $this->dienst()->codePruefen(
            Http::getrimmteEingabe('code'),
            Http::gegenstelle(),
            Http::benutzerkennung(),
        );

        if ($token === null) {
            return $this->codeFormular([], [self::MELDUNG_CODE]);
        }

        return Antwort::weiter('/admin');
    }

    /** @param array<string,string> $parameter */
    public function abmelden(array $parameter = []): Antwort
    {
        $this->dienst()->abmelden(Http::gegenstelle());

        return Antwort::weiter('/admin/anmelden');
    }

    private function dienst(): AnmeldeDienst
    {
        return $this->dienst ?? new AnmeldeDienst();
    }

    /** @return array<string,mixed>|null */
    private function betreiberdaten(): ?array
    {
        try {
            return (new BetreiberdatenSpeicher())->lesen();
        } catch (\Throwable) {
            return null;
        }
    }

    /** @param array<string,mixed>|null $zeile */
    private function wert(?array $zeile, string $feld): ?string
    {
        $wert = $zeile[$feld] ?? null;

        return is_string($wert) && trim($wert) !== '' ? trim($wert) : null;
    }
}
