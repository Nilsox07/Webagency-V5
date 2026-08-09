<?php

declare(strict_types=1);

namespace Sartu\Admin;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\AuditProtokoll;
use Sartu\Helpers\Http;
use Sartu\Services\Startsperre;
use Sartu\Services\Zahlungsschluessel;

/**
 * `/admin/ersteinrichtung` — was zwischen dem heutigen Stand und dem Onlinegang steht.
 *
 * ## Was diese Seite nicht ist
 *
 * **Sie ist nicht der neunte Schritt der Ersteinrichtung.** `14_SICHERHEIT.md` §1.5 gibt
 * **acht** Schritte vor, und `/admin/setup` ist nach dem achten dauerhaft 404. Diese Seite
 * liegt hinter der Adminanmeldung und ist beliebig oft aufrufbar — sie schliesst nichts ab.
 *
 * ## Zwei Listen, und nur eine sperrt
 *
 * Die obere kommt aus `Startsperre::hindernisse()` und hält die Veröffentlichung an
 * (§1.4a). Die untere kommt aus `weiterePunkte()`, nennt den Zahlungsschlüssel und das
 * Logo und hält **nichts** an — die Begründung steht dort.
 *
 * ## Warum der Menüpunkt verschwindet, die Seite aber bleibt
 *
 * Im Kopfband steht „Ersteinrichtung", solange `starterlaubt()` falsch ist. Danach hat der
 * Punkt seine Aufgabe erfüllt und wäre ein Eintrag, der jeden Tag an etwas Erledigtes
 * erinnert.
 *
 * Die **Seite** bleibt trotzdem erreichbar und ist von der Übersicht aus verlinkt: Auf ihr
 * steht das einzige Formular für den Zahlungsschlüssel (§6). Verschwände sie mit dem
 * Menüpunkt, gäbe es nach dem Onlinegang keinen Weg mehr, den Schlüssel zu wechseln — und
 * genau dann wird er gewechselt, nämlich beim Übergang von Test auf Produktiv.
 */
final class ErsteinrichtungSteuerung
{
    /**
     * @param array<string,string> $parameter
     * @param list<string> $fehler
     * @param list<string> $hinweise
     */
    public function zeigen(array $parameter = [], array $fehler = [], array $hinweise = []): Antwort
    {
        $sperre = new Startsperre();
        $schluessel = new Zahlungsschluessel();

        return Antwort::html(Ansicht::seite('admin', 'admin-ersteinrichtung', [
            'titel'         => 'Ersteinrichtung',
            'angemeldet'    => true,
            'hindernisse'   => $sperre->hindernisse(),
            'weiterePunkte' => $sperre->weiterePunkte(),
            'logo'          => Startsperre::logoAusgeliefert(),
            'feld'          => Zahlungsschluessel::feld(),
            // Nie der Schlüssel selbst — höchstens seine letzten vier Zeichen (Fall 93).
            'spurTest'      => $schluessel->spur(Zahlungsschluessel::FELD_TEST),
            'spurLive'      => $schluessel->spur(Zahlungsschluessel::FELD_LIVE),
            'fehler'        => $fehler,
            'hinweise'      => $hinweise,
        ]));
    }

    /** @param array<string,string> $parameter */
    public function zahlungsschluessel(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $feld = Http::getrimmteEingabe('feld');
        $fehler = (new Zahlungsschluessel())->hinterlegen($feld, Http::eingabe('schluessel') ?? '');

        if ($fehler !== []) {
            return $this->zeigen([], $fehler);
        }

        // Protokolliert wird **dass**, nie **was**. Der Schlüssel steht in keiner Zeile —
        // auch nicht im Detail, auch nicht gekürzt (Fall 93).
        (new AuditProtokoll())->schreiben(
            aktion: 'zahlungsschluessel_hinterlegt',
            objektart: 'operator_settings',
            akteurBenutzerId: $nachweis->adminBenutzerId,
            neuerWert: $feld,
            ip: Http::gegenstelle(),
        );

        return $this->zeigen([], [], ['Der Zahlungsschlüssel ist hinterlegt.']);
    }

    /** @param array<string,string> $parameter */
    public function zahlungsschluesselEntfernen(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $feld = Http::getrimmteEingabe('feld');

        if (!in_array($feld, [Zahlungsschluessel::FELD_TEST, Zahlungsschluessel::FELD_LIVE], true)) {
            return $this->zeigen([], ['Dieses Feld gibt es nicht.']);
        }

        (new Zahlungsschluessel())->entfernen($feld);

        (new AuditProtokoll())->schreiben(
            aktion: 'zahlungsschluessel_entfernt',
            objektart: 'operator_settings',
            akteurBenutzerId: $nachweis->adminBenutzerId,
            alterWert: $feld,
            ip: Http::gegenstelle(),
        );

        return $this->zeigen([], [], ['Der Zahlungsschlüssel ist entfernt. '
            . 'Neue Rechnungen gehen ohne Zahlungsweg hinaus.']);
    }
}
