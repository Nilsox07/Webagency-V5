<?php

declare(strict_types=1);

namespace Sartu\Admin;

use Sartu\Antwort;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Admin\AdminOeffnungszeiten;
use Sartu\Data\Admin\AdminProjekte;
use Sartu\Data\AuditProtokoll;
use Sartu\Helpers\Http;
use Sartu\Services\Projektmail;

/**
 * `Als veröffentlicht markieren` — Portal-Lastenheft §9.2, Abschnitt „Öffnungszeiten".
 *
 * Der Knopf sagt nicht, dass etwas passiert ist, sondern **stellt fest**, dass es passiert
 * ist: Ein Mensch hat die Änderung auf die Website gebracht. Deshalb bestätigt der Admin sie
 * hier, statt dass das Portal sie automatisch als veröffentlicht führt — §4: „Änderungen
 * gelten erst nach Rebuild als veröffentlicht."
 *
 * **Wartet nichts, geht keine Mail raus.** `alsVeroeffentlichtMarkieren()` sagt, ob es
 * überhaupt eine wartende Änderung gab. Eine Mail „Ihre Öffnungszeiten sind aktualisiert" an
 * einen Kunden, der nichts geändert hat, ist eine Falschaussage.
 */
final class OeffnungszeitenSteuerung
{
    /** @param array<string,string> $parameter */
    public function veroeffentlichen(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $projekt = (new AdminProjekte($nachweis))->finden((string) ($parameter['id'] ?? ''));

        if ($projekt === null) {
            return Antwort::nichtGefunden();
        }

        $organisationId = (string) $projekt['organization_id'];

        if (!(new AdminOeffnungszeiten($nachweis))->alsVeroeffentlichtMarkieren($organisationId)) {
            return (new ProjekteSteuerung())->einzeln(
                $parameter,
                ['Es wartet gerade keine Änderung an den Öffnungszeiten.'],
            );
        }

        (new AuditProtokoll())->schreiben(
            aktion: 'oeffnungszeiten_veroeffentlicht',
            objektart: 'organization',
            objektId: $organisationId,
            akteurBenutzerId: $nachweis->adminBenutzerId,
            organisationId: $organisationId,
            neuerWert: 'veroeffentlicht',
            ip: Http::gegenstelle(),
        );

        // §10: `Ihre Öffnungszeiten sind aktualisiert`.
        (new Projektmail())->anKunden(
            $projekt,
            'Ihre Öffnungszeiten sind aktualisiert',
            "Ihre Änderung ist jetzt auf der Website sichtbar.\n",
        );

        return (new ProjekteSteuerung())->einzeln(
            $parameter,
            [],
            ['Die Öffnungszeiten sind als veröffentlicht vermerkt. Der Kunde hat Bescheid.'],
        );
    }
}
