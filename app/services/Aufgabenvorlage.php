<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\Admin\AdminAufgaben;
use Sartu\Data\Admin\AdminNachweis;

/**
 * Die Aufgabenliste, die ein Projekt beim Produktionsstart bekommt — §9.3.
 *
 * ## Warum eine Vorlage und keine leere Liste
 *
 * §8.3: „Wir haben vorausgefuellt, was wir schon ueber Ihr Unternehmen wissen. Sie
 * bestaetigen es oder korrigieren es." Ein Kunde, der eine leere Aufgabenliste sieht, weiss
 * nicht, was von ihm gebraucht wird — und ruft an.
 *
 * ## Was hier NICHT steht
 *
 * §9.3 nennt eine durchnummerierte Liste bis Nr. 13. Die Nummern und Titel dazwischen
 * stehen im Website-Lastenheft in wechselnder Ausfuehrlichkeit; erfunden wird keiner.
 * Aufgenommen sind die Punkte, die dort **benannt** sind, plus die Freigabeaufgabe, die
 * §9.3 ausdruecklich als Nr. 13 mit `kind = freigabe` fuehrt.
 *
 * Der Admin kann jederzeit weitere anlegen — die Vorlage ist ein Anfang, keine Grenze.
 */
final class Aufgabenvorlage
{
    /**
     * @return list<array{title:string,description:string,why_needed:string,kind:string,required:bool}>
     */
    public static function standard(): array
    {
        return [
            [
                'title'      => 'Ihre Firmendaten bestätigen',
                'description' => 'Firmenname, Anschrift, Telefon und E-Mail, wie sie auf der '
                    . 'Website stehen sollen.',
                'why_needed' => 'Diese Angaben stehen im Impressum und im Kontaktbereich.',
                'kind'       => 'bestaetigung',
                'required'   => true,
            ],
            [
                'title'      => 'Ihre Leistungen beschreiben',
                'description' => 'Was bieten Sie an? Ein bis drei Sätze je Leistung genügen.',
                'why_needed' => 'Daraus entstehen die Leistungsseiten und ihre Texte.',
                'kind'       => 'angabe',
                'required'   => true,
            ],
            [
                'title'      => 'Ihr Einsatzgebiet',
                'description' => 'In welchen Orten und Regionen arbeiten Sie?',
                'why_needed' => 'Danach richtet sich, wo Ihre Website gefunden wird.',
                'kind'       => 'angabe',
                'required'   => true,
            ],
            [
                'title'      => 'Bilder und Unterlagen hochladen',
                'description' => 'Fotos von Ihrer Arbeit, Ihr Logo, vorhandene Unterlagen.',
                'why_needed' => 'Ohne eigene Bilder wirkt eine Website austauschbar.',
                'kind'       => 'upload',
                'required'   => false,
            ],
            [
                'title'      => 'Fakten und Umfang final freigeben',
                'description' => 'Der letzte Schritt vor der Produktion.',
                'why_needed' => 'Ab Ihrer Freigabe läuft der vereinbarte Zeitrahmen.',
                'kind'       => 'freigabe',
                'required'   => true,
            ],
        ];
    }

    /**
     * Legt die Vorlage fuer ein Projekt an — einmal.
     *
     * @return int wie viele Aufgaben entstanden sind
     */
    public static function anlegen(AdminNachweis $nachweis, string $projektId, ?\PDO $pdo = null): int
    {
        $aufgaben = new AdminAufgaben($nachweis, $pdo);

        if ($aufgaben->jeProjekt($projektId) !== []) {
            // Ein zweiter Aufruf legt nichts nach: Der Kunde haette sonst jede Aufgabe
            // doppelt.
            return 0;
        }

        $nummer = 0;

        foreach (self::standard() as $vorlage) {
            $aufgaben->anlegen([
                'project_id'  => $projektId,
                'title'       => $vorlage['title'],
                'description' => $vorlage['description'],
                'why_needed'  => $vorlage['why_needed'],
                'kind'        => $vorlage['kind'],
                'status'      => 'offen',
                'sort_order'  => ++$nummer,
                'required'    => $vorlage['required'] ? 1 : 0,
            ]);
        }

        return $nummer;
    }
}
