<?php

declare(strict_types=1);

namespace Sartu\Admin;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\Admin\AdminAufgaben;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Services\Nachrichtendienst;

/**
 * Support-Nachrichten im internen Bereich — Portal-Lastenheft §9.1.
 *
 * Der Kunde schreibt unter `/portal/hilfe`, die Antwort entsteht hier. §8.9 verspricht ihm
 * „schriftlich, in der Regel innerhalb eines Werktags" — bis zum 02.08.2026 gab es dafür
 * keine Maske und keinen Versand.
 *
 * **Adminschicht, keine gemeinsame Abfrage mit der Kundenseite** (§3 Regel 2). Der Admin
 * sieht alle Organisationen; der Kunde sieht über `KundenNachrichten` ausschließlich seine
 * eigene. Zwei Klassen, kein Schalter.
 */
final class NachrichtenSteuerung
{
    /**
     * @param array<string,string> $parameter
     * @param list<string> $fehler
     * @param list<string> $hinweise
     */
    public function liste(array $parameter = [], array $fehler = [], array $hinweise = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        return Antwort::html(Ansicht::seite('admin', 'admin-nachrichten', [
            'titel'       => 'Nachrichten',
            'angemeldet'  => true,
            'nachrichten' => (new AdminAufgaben($nachweis))->alleNachrichten(),
            'fehler'      => $fehler,
            'hinweise'    => $hinweise,
        ]));
    }

    /** @param array<string,string> $parameter */
    public function antworten(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $antwort = $_POST['answer_text'] ?? '';

        $fehler = (new Nachrichtendienst($nachweis))->beantworten(
            (string) ($parameter['id'] ?? ''),
            is_string($antwort) ? $antwort : '',
        );

        if ($fehler !== []) {
            return $this->liste($parameter, $fehler);
        }

        return $this->liste($parameter, [], ['Antwort gesendet.']);
    }
}
