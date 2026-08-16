<?php

declare(strict_types=1);

namespace Sartu;

final class Route
{
    public const BEREICH_OEFFENTLICH = 'oeffentlich';
    public const BEREICH_PORTAL      = 'portal';
    public const BEREICH_ADMIN       = 'admin';
    public const BEREICH_API         = 'api';

    /** @param callable():Antwort|array{0:class-string,1:string} $handler */
    public function __construct(
        public readonly string $bereich,
        public readonly string $methode,
        public readonly string $pfad,
        public readonly mixed $handler,
        /**
         * Nur fuer die Ersteinrichtung. Sie ist die einzige Adminroute ohne Anmeldung —
         * es gibt zu diesem Zeitpunkt noch kein Konto. Ihre eigene Sperre steht in
         * InstallationsSperre und ist strenger als jede Anmeldung (§1.5).
         */
        public readonly bool $ohneAnmeldung = false,
        /**
         * Nur fuer den Zahlungs-Webhook. **Die einzige Route ohne CSRF-Pruefung.**
         *
         * ## Warum es diesen Schalter ueberhaupt gibt
         *
         * `14_SICHERHEIT.md` Regel 3 sagt: „CSRF-Token bei jedem `POST`. Kein Token, keine
         * Ausnahme." Der Satz danach nennt den Grund und zugleich seinen Geltungsbereich:
         * „Jede Aktion ist ein **normales Formular**."
         *
         * `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 6 Schritt 3 verlangt zugleich, dass ein
         * fremder Server eine Adresse aufruft. Er hat keine Sitzung, kein Formular und kann
         * kein Token haben. **Beide Vorgaben zugleich sind nicht erfuellbar** — das ist als
         * Abweichung in `IMPLEMENTATION_SUMMARY.md` festgehalten und nicht stillschweigend
         * aufgeloest.
         *
         * ## Warum die Ausnahme hier nichts oeffnet
         *
         * CSRF schuetzt davor, dass ein Dritter eine Handlung **im Namen einer fremden
         * Sitzung** ausloest. Diese Route hat keine Sitzung, liest keine und schreibt keine.
         * Sie nimmt eine Kennung entgegen und tut damit genau eines: den Zahlungsdienst
         * fragen. Was **er** antwortet, entscheidet — nicht der Aufruf. Wer die Adresse
         * faelscht, erreicht einen Abruf ins Leere.
         *
         * Deshalb der Schalter je Route und nicht je Bereich: Eine zweite `/api/`-Route
         * bekommt die Ausnahme nicht dadurch, dass sie unter `/api/` liegt.
         * `TenantIsolationTest` haelt fest, dass es bei genau einer bleibt.
         */
        public readonly bool $ohneCsrf = false,
        /**
         * **Braucht diese Route eine Sitzung?** Vorgabe: nein.
         *
         * ## Warum als Positiventscheidung je Route
         *
         * Bis zum 16.08.2026 entschied eine Negativliste `Sitzung::OHNE_SITZUNG` mit drei
         * Wurzeldateien und `/api/`. Alles andere bekam eine Sitzung — auch die Startseite,
         * `/preise` und jede Rechtsseite. Gemessen: `Set-Cookie: PHPSESSID` auf jeder
         * öffentlichen Seite.
         *
         * **§ 25 TDDDG erlaubt die Speicherung auf dem Endgerät ohne Einwilligung nur,
         * soweit sie für den vom Nutzer ausdrücklich gewünschten Dienst unbedingt
         * erforderlich ist.** Eine Leseseite braucht keine Sitzung; ein Formular mit
         * CSRF-Schutz braucht eine. Der Unterschied gehört deshalb an die Route und nicht in
         * eine Liste von Ausnahmen: Eine neue Leseseite ist damit von sich aus cookiefrei,
         * und eine neue Formularroute muss die Sitzung ausdrücklich anfordern.
         *
         * **Jedes `POST` bekommt sie unabhängig davon**, weil der CSRF-Schutz sie braucht —
         * ausgenommen der Zahlungs-Webhook, der `ohneCsrf` trägt. Das entscheidet der
         * Router, nicht diese Angabe.
         */
        public readonly bool $sitzung = false,
    ) {
    }

    public function schluessel(): string
    {
        return $this->methode . ' ' . $this->pfad;
    }
}
