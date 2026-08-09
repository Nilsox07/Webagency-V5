<?php

declare(strict_types=1);

namespace Sartu\Api;

use Sartu\Antwort;
use Sartu\Services\Mollie;
use Sartu\Services\Zahlungsabgleich;
use Sartu\Helpers\Http;

/**
 * Der Webhook des Zahlungsdienstes — `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 6.
 *
 * ## Die erste Route unter `/api/`
 *
 * `14_SICHERHEIT.md` sieht `/api/` als eigenen Bereich neben Kunden- und Adminbereich vor.
 * Bis hierher war er leer, weil es nichts gab, was ohne Browser hereinkommt.
 *
 * ## Was diese Steuerung tut — und was ausdrücklich nicht
 *
 * Sie liest **eine** Angabe: die Zahlungskennung. Alles Weitere holt der Server selbst.
 *
 * Sie liest **keinen** behaupteten Zustand, **keinen** Betrag und **keine**
 * Rechnungskennung, auch nicht als Nebenangabe. §6: „Der Webhook ist ein Klingelzeichen,
 * keine Aussage." Ein Feld, das hier gelesen würde, wäre eine Aussage — und die Regel aus
 * §12, dass der Zahlungsstatus nie aus einer Rückkehr abgeleitet wird, hinge dann daran,
 * dass niemand es benutzt.
 *
 * ## Warum die Antwort so wenig sagt
 *
 * Am anderen Ende steht eine Maschine, die den Statuscode liest. Ein ausführlicher Text
 * würde nur einem Menschen nützen, der diese Adresse von Hand aufruft — und dem soll sie
 * nichts über den Zustand einer Rechnung verraten.
 */
final class ZahlungenSteuerung
{
    /** @param array<string,string> $parameter */
    public function mollie(array $parameter = []): Antwort
    {
        unset($parameter);

        // Mollie schickt `id` als Formularfeld. Gelesen wird genau dieses eine Feld.
        $kennung = Http::eingabe('id');
        $rumpf = self::rumpf();

        $ergebnis = (new Zahlungsabgleich(new Mollie()))->verarbeiten(
            is_string($kennung) ? $kennung : '',
            $rumpf,
            Http::gegenstelle(),
        );

        return Antwort::text($ergebnis['text'], $ergebnis['status']);
    }

    /**
     * Der Rumpf, wie er ankam — nur für den Hash in `payment_events`.
     *
     * Er wird nicht ausgewertet. `php://input` steht im Test nicht zur Verfügung; dann ist
     * die Rekonstruktion aus `$_POST` gut genug für einen Vergleich „dieselbe Nachricht?".
     */
    private static function rumpf(): string
    {
        $roh = @file_get_contents('php://input');

        if (is_string($roh) && $roh !== '') {
            return $roh;
        }

        return http_build_query($_POST);
    }
}
