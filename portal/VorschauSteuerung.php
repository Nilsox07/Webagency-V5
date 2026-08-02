<?php

declare(strict_types=1);

namespace Sartu\Portal;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\Customer\KundenFreigaben;
use Sartu\Data\Customer\KundenProjekte;
use Sartu\Data\Customer\KundenVorschau;
use Sartu\Helpers\Http;
use Sartu\Services\Vorschaudienst;
use Sartu\Sitzung;

/**
 * `/portal/vorschau` und `/portal/domain` — Portal-Lastenheft §8.4 und §8.7.
 *
 * Die Projektkennung kommt aus dem aktuellen Projekt der Sitzungsorganisation, nie aus der
 * Adresse. Es gibt deshalb keine Route mit `{projekt}` — §3 Regel 1 waere sonst eine Frage
 * der Sorgfalt statt eine der Signatur.
 */
final class VorschauSteuerung
{
    /**
     * @param array<string,string> $parameter
     * @param list<string> $fehler
     */
    public function vorschau(array $parameter = [], array $fehler = []): Antwort
    {
        $bereich = KundenBereich::ausSitzung();
        $projekt = (new KundenProjekte($bereich))->aktuelles();

        if ($projekt === null) {
            return Antwort::html(Ansicht::seite('portal', 'portal-vorschau', [
                'titel' => 'Vorschau und Freigabe', 'angemeldet' => true,
                'projekt' => null, 'runde' => null, 'rueckmeldungen' => [], 'runden' => [],
                'abnahme' => null, 'fehler' => $fehler,
            ]));
        }

        $vorschau = new KundenVorschau($bereich);
        $runde = $vorschau->aktuelleRunde((string) $projekt['id']);

        return Antwort::html(Ansicht::seite('portal', 'portal-vorschau', [
            'titel'          => 'Vorschau und Freigabe',
            'angemeldet'     => true,
            'projekt'        => $projekt,
            'runde'          => $runde,
            'runden'         => $vorschau->runden((string) $projekt['id']),
            'rueckmeldungen' => $runde === null ? [] : $vorschau->rueckmeldungen((string) $runde['id']),
            'abnahme'        => (new KundenFreigaben($bereich))
                ->finden((string) $projekt['id'], KundenFreigaben::ABNAHME),
            'fehler'         => $fehler,
        ]));
    }

    /** @param array<string,string> $parameter */
    public function rueckmeldung(array $parameter = []): Antwort
    {
        return $this->aktion(static fn (Vorschaudienst $d, string $projektId, string $benutzerId, ?string $ip): array
            => $d->rueckmeldungSenden($projektId, $_POST, $benutzerId));
    }

    /** @param array<string,string> $parameter */
    public function einreichen(array $parameter = []): Antwort
    {
        return $this->aktion(static fn (Vorschaudienst $d, string $projektId, string $benutzerId, ?string $ip): array
            => $d->einreichen($projektId, $benutzerId, $ip));
    }

    /** @param array<string,string> $parameter */
    public function abnehmen(array $parameter = []): Antwort
    {
        return $this->aktion(static fn (Vorschaudienst $d, string $projektId, string $benutzerId, ?string $ip): array
            => $d->abnehmen($projektId, $_POST, $benutzerId, $ip));
    }

    /** §8.7 — die Domainlage, vom Admin gepflegt, vom Kunden gelesen. */
    public function domain(array $parameter = []): Antwort
    {
        $bereich = KundenBereich::ausSitzung();
        $projekt = (new KundenProjekte($bereich))->aktuelles();

        return Antwort::html(Ansicht::seite('portal', 'portal-domain', [
            'titel'      => 'Domain',
            'angemeldet' => true,
            'projekt'    => $projekt,
            'stand'      => $projekt === null
                ? null
                : (new KundenVorschau($bereich))->domainstand((string) $projekt['id']),
        ]));
    }

    private function aktion(\Closure $tun): Antwort
    {
        $bereich = KundenBereich::ausSitzung();
        $projekt = (new KundenProjekte($bereich))->aktuelles();

        if ($projekt === null) {
            return Antwort::nichtGefunden();
        }

        $ip = Http::gegenstelle() === '' ? null : Http::gegenstelle();

        $fehler = $tun(
            new Vorschaudienst($bereich),
            (string) $projekt['id'],
            (string) Sitzung::wert(Sitzung::BENUTZER),
            $ip,
        );

        return $fehler === []
            ? Antwort::weiter('/portal/vorschau', 303)
            : $this->vorschau([], $fehler);
    }
}
