<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\AnfrageSpeicher;
use Sartu\Data\AuditProtokoll;

/**
 * Der tägliche Löschlauf — Portal-Lastenheft §15.1 und §4b.4.
 *
 * Zwei Aufgaben, die oft für eine gehalten werden:
 *
 * | Nach | Was passiert | Testfall |
 * |---|---|---|
 * | **30 Tagen** | `source_ip` wird geleert, **der übrige Datensatz bleibt** | 40 |
 * | `delete_after` | die Anfrage wird **vollständig** gelöscht | 80 |
 *
 * **Die erste ist keine Löschung, sondern eine Kürzung.** Die IP dient der Missbrauchsabwehr
 * und dem Nachweis der Einwilligung — beides ist nach dreißig Tagen erledigt. Wer den ganzen
 * Datensatz mitnimmt, verliert die Anfrage, die noch elf Monate gelten soll.
 *
 * **Umgewandelte Anfragen werden nie automatisch gelöscht.** Sie sind Teil der Kundenakte
 * (§4b.4). Die Bedingung dafür ist `converted_organization_id IS NOT NULL` und steht in der
 * Abfrage, nicht hier — sonst hinge sie an einem `if`, das jemand später wegnimmt.
 *
 * **Das Audit-Ereignis nennt die Kennung und sonst nichts.** §15.1: „vollständige Löschung
 * mit Audit-Ereignis **ohne** die gelöschten Inhalte." Ein Protokoll, das Name und Adresse
 * mitschreibt, hebt die Löschung auf.
 */
final class Loeschlauf
{
    /** §4b.4 und §15.1: die IP wird nach dreissig Tagen geleert. */
    public const IP_TAGE = 30;

    public function __construct(
        private readonly ?AnfrageSpeicher $speicher = null,
        private readonly ?AuditProtokoll $audit = null,
    ) {
    }

    /** @return array{ip_geleert:int,geloescht:int} */
    public function ausfuehren(?\DateTimeImmutable $jetzt = null): array
    {
        $jetzt ??= new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $grenze = $jetzt->modify('-' . self::IP_TAGE . ' days')->format('Y-m-d H:i:s');
        $ipGeleert = $this->speicher()->herkunftsadressenLeeren($grenze);

        $faellig = $this->speicher()->faelligeIds($jetzt->format('Y-m-d'));

        foreach ($faellig as $id) {
            $this->speicher()->endgueltigLoeschen($id);

            $this->audit()->schreiben(
                aktion: 'anfrage_frist_geloescht',
                objektart: 'lead',
                objektId: $id,
                grund: 'Löschfrist nach §15.1 abgelaufen',
            );
        }

        return ['ip_geleert' => $ipGeleert, 'geloescht' => count($faellig)];
    }

    private function speicher(): AnfrageSpeicher
    {
        return $this->speicher ?? new AnfrageSpeicher();
    }

    private function audit(): AuditProtokoll
    {
        return $this->audit ?? new AuditProtokoll();
    }
}
