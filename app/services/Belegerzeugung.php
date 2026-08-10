<?php

declare(strict_types=1);

namespace Sartu\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use horstoeko\zugferd\ZugferdDocumentPdfBuilder;
use Sartu\Ansicht;
use Sartu\Data\Belege;
use Sartu\Data\Db;
use Sartu\Helpers\Speicher;

/**
 * Belege erzeugen, prüfen und ablegen — `18_BELEGE_UND_ZAHLUNG.md` Abschnitte 2, 3 und 7.
 *
 * ## Die Reihenfolge ist die Aussage
 *
 * 1. Sichtseite als HTML aus `app/views/belege/`, mit `design/tokens.css` und dem Logo
 * 2. HTML nach PDF
 * 3. XML nach EN 16931 aufbauen und **prüfen**
 * 4. **Fällt die Prüfung durch, bricht der Vorgang hier ab** — es entsteht keine Datei
 * 5. XML ins PDF einbetten, PDF/A-3
 * 6. Ablegen unter `/storage`, Prüfsumme in `documents`
 *
 * §3: „Fällt die Prüfung durch, bricht der Versand ab. Es gibt **keinen** Weg, eine ungültige
 * Rechnung zu senden — auch nicht mit Bestätigung." Schritt 4 steht deshalb **vor** Schritt 6:
 * Eine ungültige Rechnung darf nicht einmal auf der Platte liegen, wo sie jemand später
 * findet und für gültig hält.
 *
 * ## Was hier NICHT geht
 *
 * §2: „Das Dokument wird **erzeugt, nicht hochgeladen**. Es gibt keinen Weg, ein fremdes PDF
 * als Rechnung einzuhängen." Diese Klasse nimmt keine Datei entgegen — sie hat keinen
 * Parameter dafür.
 *
 * **Ein Beleg wird nie neu erzeugt.** §7: „Eine zweite Erzeugung ist ein zweites Dokument."
 * Wer zweimal erzeugt, bekommt zwei Zeilen in `documents` — und sieht das.
 *
 * ## Warum dompdf kein Netz braucht
 *
 * `isRemoteEnabled` bleibt **aus**. Das Blatt und das Logo werden eingelesen und mitgegeben;
 * eine Vorlage, die beim Erzeugen etwas nachlädt, wäre von einer erreichbaren Adresse
 * abhängig — und ein Beleg, der ohne Netz anders aussieht, ist kein Beleg.
 */
final class Belegerzeugung
{
    /** Der Dateiname des eingebetteten XML — von der Norm vorgegeben. */
    public const XML_NAME = 'factur-x.xml';

    public function __construct(
        private readonly ?Belege $belege = null,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * Erzeugt den Beleg zu einer Rechnung, prüft ihn und legt ihn ab.
     *
     * @param array<string,mixed> $rechnung
     * @param array<string,mixed> $betreiber
     * @param array<string,mixed> $organisation
     * @param array<string,mixed>|null $projekt
     *
     * @return array{fehler:list<string>,id:?string,pfad:?string}
     */
    public function rechnung(
        array $rechnung,
        array $betreiber,
        array $organisation,
        ?array $projekt = null,
    ): array {
        $storno = ($rechnung['cancels_invoice_id'] ?? null) !== null;
        $positionen = self::positionen($rechnung, $projekt);

        $erechnung = new ERechnung($rechnung, $betreiber, $organisation, $positionen);

        // Schritt 3 und 4 — **vor** jedem Schreiben.
        $verletzt = $erechnung->pruefen();

        if ($verletzt !== []) {
            return [
                'fehler' => array_merge(
                    ['Der Beleg entspricht nicht der Norm EN 16931 und wurde deshalb nicht erzeugt.'],
                    $verletzt,
                ),
                'id'    => null,
                'pfad'  => null,
            ];
        }

        $sichtseite = Ansicht::teil('belege/rechnung', [
            'rechnung'     => $rechnung,
            'betreiber'    => $betreiber,
            'organisation' => $organisation,
            'projekt'      => $projekt,
            'positionen'   => $positionen,
            'tokens'       => self::gestaltungswerte(),
            'logo'         => self::logo(),
            'ueberschrift' => $storno ? 'Stornorechnung' : 'Rechnung',
            'hinweis'      => $storno
                ? 'Diese Stornorechnung hebt die oben genannte Rechnung auf. Ein Betrag ist nicht zu zahlen.'
                : 'Bitte überweisen Sie den Gesamtbetrag bis zum angegebenen Datum.',
        ]);

        $pdf = self::nachPdf($sichtseite);

        $bauer = new ZugferdDocumentPdfBuilder($erechnung->rohbau(), $pdf);
        $bauer->generateDocument();

        return $this->ablegen(
            $storno ? 'storno' : 'rechnung',
            (string) $rechnung['number'],
            $bauer->downloadString(),
            'pdfa3-zugferd',
            rechnungId: (string) $rechnung['id'],
        );
    }

    /**
     * Der reine XML-Nebenweg — §3: „erlaubt, wenn ein Kunde ausdrücklich XML ohne PDF
     * verlangt. Er entsteht aus demselben Datensatz und darf nie getrennt gepflegt werden."
     *
     * Deshalb dieselbe `ERechnung` wie oben, kein zweiter Aufbau.
     *
     * @param array<string,mixed> $rechnung
     * @param array<string,mixed> $betreiber
     * @param array<string,mixed> $organisation
     * @param array<string,mixed>|null $projekt
     */
    public function xmlAllein(array $rechnung, array $betreiber, array $organisation, ?array $projekt = null): string
    {
        return (new ERechnung($rechnung, $betreiber, $organisation, self::positionen($rechnung, $projekt)))->xml();
    }

    /**
     * Prüft einen abgelegten Beleg gegen seine Prüfsumme — §7, Fall 94.
     *
     * §7: „Weicht sie beim Abruf ab, ist das ein **Fehler** und keine Warnung." Diese Methode
     * gibt deshalb den Inhalt **oder** wirft — sie hat keinen dritten Ausgang, an dem ein
     * Aufrufer weiterlesen könnte.
     *
     * @param array<string,mixed> $beleg
     * @throws \RuntimeException wenn die Datei fehlt oder abweicht
     */
    public function inhalt(array $beleg): string
    {
        // `basename()` als Tiefenschutz. Der Wert kommt heute aus `documents` und wurde von
        // `ablegen()` erzeugt — er kann keinen Pfadanteil tragen. Der Schutz kostet nichts
        // und haelt, falls je ein zweiter Schreibweg dazukommt.
        $pfad = self::verzeichnis() . '/' . basename(str_replace('\\', '/', (string) $beleg['path']));

        if (!is_file($pfad)) {
            throw new \RuntimeException('Der Beleg ' . (string) $beleg['number'] . ' fehlt in der Ablage.');
        }

        $inhalt = (string) file_get_contents($pfad);

        if (hash('sha256', $inhalt) !== (string) $beleg['checksum']) {
            throw new \RuntimeException(
                'Der Beleg ' . (string) $beleg['number'] . ' weicht von seiner Prüfsumme ab.'
            );
        }

        return $inhalt;
    }

    // ---------------------------------------------------------------- intern

    /**
     * @return array{fehler:list<string>,id:?string,pfad:?string}
     */
    private function ablegen(
        string $art,
        string $nummer,
        string $inhalt,
        string $format,
        ?string $rechnungId = null,
        ?string $angebotId = null,
    ): array {
        $verzeichnis = self::verzeichnis();

        if (!is_dir($verzeichnis) && !mkdir($verzeichnis, 0770, true) && !is_dir($verzeichnis)) {
            return ['fehler' => ['Die Belegablage lässt sich nicht anlegen.'], 'id' => null, 'pfad' => null];
        }

        // Der Dateiname trägt die Nummer und eine Kennung. Die Nummer allein wäre beim
        // zweiten Erzeugen derselbe Name — und §7 verbietet das Überschreiben.
        $name = $nummer . '-' . bin2hex(random_bytes(6)) . '.pdf';

        if (file_put_contents($verzeichnis . '/' . $name, $inhalt) === false) {
            return ['fehler' => ['Der Beleg lässt sich nicht ablegen.'], 'id' => null, 'pfad' => null];
        }

        $id = $this->belege()->anlegen([
            'kind'       => $art,
            'number'     => $nummer,
            'invoice_id' => $rechnungId,
            'offer_id'   => $angebotId,
            'path'       => $name,
            'checksum'   => hash('sha256', $inhalt),
            'format'     => $format,
        ]);

        return ['fehler' => [], 'id' => $id, 'pfad' => $name];
    }

    /**
     * Die Positionen einer Rechnung.
     *
     * §2: „Art und Umfang der Leistung — Meilenstein und Leistungstext aus dem zugehörigen
     * Angebot." Eine Rechnung trägt heute **einen** Betrag; die Position ist deshalb eine,
     * und ihr Text nennt Meilenstein und Projekt.
     *
     * @param array<string,mixed> $rechnung
     * @param array<string,mixed>|null $projekt
     * @return list<array{bezeichnung:string,netto:int}>
     */
    private static function positionen(array $rechnung, ?array $projekt): array
    {
        $meilenstein = Rechnungsdienst::MEILENSTEINE[(string) $rechnung['milestone']] ?? 'Leistung';
        $titel = $projekt === null ? '' : trim((string) $projekt['title']);

        return [[
            'bezeichnung' => $titel === '' ? $meilenstein : $meilenstein . ' — ' . $titel,
            'netto'       => (int) $rechnung['net_cents'],
        ]];
    }

    private static function nachPdf(string $html): string
    {
        $einstellungen = new Options();
        // Kein Netz. Alles, was der Beleg braucht, ist im HTML.
        $einstellungen->set('isRemoteEnabled', false);
        $einstellungen->set('isHtml5ParserEnabled', true);
        $einstellungen->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($einstellungen);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return (string) $dompdf->output();
    }

    /** `design/tokens.css` — die Quelle, nicht die Auslieferungskopie. */
    private static function gestaltungswerte(): string
    {
        $pfad = dirname(__DIR__, 2) . '/design/tokens.css';

        return is_file($pfad) ? (string) file_get_contents($pfad) : '';
    }

    /**
     * Das Logo als `data:`-Adresse.
     *
     * dompdf lädt ohne `isRemoteEnabled` keine Adresse nach — und soll es auch nicht. Eine
     * eingebettete Kopie macht den Beleg unabhängig davon, ob der Webserver gerade läuft.
     */
    private static function logo(): string
    {
        $pfad = dirname(__DIR__, 2) . '/public/assets/bild/sartu-logo-dunkel.svg';

        if (!is_file($pfad)) {
            return '';
        }

        return 'data:image/svg+xml;base64,' . base64_encode((string) file_get_contents($pfad));
    }

    private static function verzeichnis(): string
    {
        return Speicher::verzeichnis() . '/belege';
    }

    private function belege(): Belege
    {
        return $this->belege ?? new Belege($this->pdo);
    }
}
