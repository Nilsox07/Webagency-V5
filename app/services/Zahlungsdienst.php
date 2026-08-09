<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Was der Server vom Zahlungsdienst braucht — und mehr nicht.
 *
 * Zwei Verben, weil `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 6 genau zwei nennt: eine Zahlung
 * **anlegen** (Schritt 1) und ihren Zustand **abrufen** (Schritt 4). Es gibt bewusst keine
 * Methode, die einen Zustand entgegennimmt: Der Webhook ist ein Klingelzeichen, keine
 * Aussage — eine Schnittstelle, die einen behaupteten Zustand annehmen kann, ist der erste
 * Schritt dahin, ihn zu glauben.
 *
 * Die Schnittstelle steht getrennt von `Mollie`, damit die Ablaufpruefung ohne Netz laeuft.
 * §16 der Sicherheitsvorgabe verlangt Tests gegen echtes MySQL, nicht gegen echte fremde
 * Server; ein Test, der einen Fremdanbieter braucht, wird beim ersten Ausfall abgeschaltet.
 */
interface Zahlungsdienst
{
    /**
     * Legt eine Zahlung an — Schritt 1.
     *
     * @param int $betragCent Bruttobetrag der Rechnung
     * @param string $referenz die Rechnungsnummer
     *
     * @return array{kennung:string,adresse:?string}
     * @throws ZahlungsdienstFehler wenn der Dienst nicht erreichbar ist oder ablehnt
     */
    public function zahlungAnlegen(
        int $betragCent,
        string $waehrung,
        string $referenz,
        string $rueckkehr,
        string $webhook,
    ): array;

    /**
     * Ruft den Zustand einer Zahlung ab — Schritt 4, mit dem eigenen Schluessel.
     *
     * @return array{kennung:string,zustand:string,betrag_cents:int,waehrung:string,bezahlt_am:?string,referenz:?string}
     * @throws ZahlungsdienstFehler
     */
    public function zahlungLesen(string $kennung): array;
}
