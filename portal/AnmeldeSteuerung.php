<?php

declare(strict_types=1);

namespace Sartu\Portal;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\SitzungsSpeicher;
use Sartu\Helpers\Http;
use Sartu\Helpers\Validate;
use Sartu\Services\AnmeldeDienst;
use Sartu\Services\KundenAnmeldung;
use Sartu\Sitzung;

/**
 * Anmeldung ohne Passwort — Portal-Lastenheft §6.
 *
 * **Die Bestätigungsseite ist immer dieselbe.** Ob es zu der Adresse einen Zugang gibt,
 * steht nirgends in der Antwort — nicht im Text, nicht im Statuscode, nicht in einer
 * zusätzlichen Zeile (Testfall 10). Der einzige abweichende Fall ist die erreichte
 * Begrenzung, und den nennt §6.2 ausdrücklich.
 *
 * **Der Notweg steht auf beiden Seiten** (§6.3). Er kommt aus den Betreiberdaten, nie aus
 * dem Quelltext (Testfall 83).
 */
final class AnmeldeSteuerung
{
    public function __construct(private readonly ?KundenAnmeldung $dienst = null)
    {
    }

    /** @param array<string,string> $parameter */
    public function formular(array $parameter = [], ?string $fehler = null, ?string $hinweis = null): Antwort
    {
        // Wer angemeldet ist, hat auf der Anmeldeseite nichts zu suchen.
        if (Sitzung::wert(Sitzung::ROLLE) === 'kunde' && Sitzung::wert(Sitzung::ORGANISATION) !== null) {
            return Antwort::weiter('/portal');
        }

        return Antwort::html(Ansicht::seite('portal', 'login', [
            'titel'   => 'Anmelden',
            'notweg'  => $this->dienst()->notweg(),
            'fehler'  => $fehler,
            'hinweis' => $hinweis ?? (($_GET['abgemeldet'] ?? null) === '1' ? 'Sie sind abgemeldet.' : null),
            'wert'    => '',
        ]));
    }

    /** @param array<string,string> $parameter */
    public function anfordern(array $parameter = []): Antwort
    {
        $email = Http::getrimmteEingabe('email');

        if (!Validate::email($email)) {
            return $this->formular([], 'Bitte geben Sie eine gültige E-Mail-Adresse an, z. B. name@firma.de');
        }

        $erlaubt = $this->dienst()->linkAnfordern($email, self::ip());

        if (!$erlaubt) {
            return $this->formular(
                [],
                'Zu viele Anfragen. Bitte versuchen Sie es in einer Stunde erneut oder rufen Sie uns an.',
            );
        }

        return Antwort::html(Ansicht::seite('portal', 'login-gesendet', [
            'titel'  => 'Prüfen Sie Ihr Postfach',
            'notweg' => $this->dienst()->notweg(),
        ]));
    }

    /** @param array<string,string> $parameter */
    public function einloesen(array $parameter = []): Antwort
    {
        $token = (string) ($parameter['token'] ?? '');

        if ($token === '' || !$this->dienst()->einloesen($token, self::ip(), Http::benutzerkennung())) {
            return Antwort::html(Ansicht::seite('portal', 'login-abgelaufen', [
                'titel' => 'Dieser Link gilt nicht mehr',
            ]), 410);
        }

        $benutzerId = (string) Sitzung::wert(Sitzung::BENUTZER);

        // §6.1 Punkt 5: Erster Login → Willkommensstrecke. Sonst → Übersicht.
        return Antwort::weiter($this->dienst()->ersterLogin($benutzerId) ? '/willkommen/1' : '/portal', 303);
    }

    /** @param array<string,string> $parameter */
    public function abmelden(array $parameter = []): Antwort
    {
        $token = $_SESSION[AnmeldeDienst::SITZUNGSTOKEN] ?? null;

        if (is_string($token) && $token !== '') {
            // §3 Regel 6: bei Abmeldung serverseitig geloescht. Ohne diese Zeile bliebe die
            // Anmeldung gueltig, solange das Cookie gilt.
            (new SitzungsSpeicher())->loeschen($token);
        }

        Sitzung::abmelden();

        return Antwort::weiter('/login?abgemeldet=1', 303);
    }

    private static function ip(): ?string
    {
        $ip = Http::gegenstelle();

        return $ip === '' ? null : $ip;
    }

    private function dienst(): KundenAnmeldung
    {
        return $this->dienst ?? new KundenAnmeldung();
    }
}
