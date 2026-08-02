<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Das Lexikon — Website-Lastenheft §13, acht Startbegriffe.
 *
 * **Auswahlregel:** „nur Begriffe, die in einem echten Verkaufsgespräch vorkommen und bei
 * denen ein Missverständnis Geld kostet. **Nicht** jeder Fachbegriff, den es gibt."
 *
 * **Acht Teile je Begriff, verbindlich:** H1 = Begriff · Kurzdefinition (2–3 Sätze) · Warum
 * es für Firmenwebsites wichtig ist · Beispiel · Typischer Fehler · Wie SARTU damit umgeht ·
 * Verwandte Begriffe (2–4) · Link zur passenden Leistungsseite.
 *
 * **Kein Suchfeld** bei acht Begriffen (§13, erst ab etwa 40). Die zwölf Begriffe der Stufe 2
 * stehen dort namentlich und werden hier **nicht** vorbereitend angelegt.
 */
final class Lexikon
{
    public const HUB_TITEL = 'Website-Lexikon: Begriffe für Firmenwebsites | SARTU';

    public const HUB_BESCHREIBUNG = 'Acht Begriffe, die in jedem Websitegespräch vorkommen — '
        . 'Firmenwebsite, Festpreis, Hosting, Domain, Relaunch, Barrierefreiheit, Local SEO '
        . 'und GEO. Je in zwei Sätzen erklärt.';

    public const HUB_H1 = 'Website-Lexikon';

    public const HUB_INTRO = 'Acht Begriffe, bei denen ein Missverständnis Geld kostet. Jeder '
        . 'in zwei Sätzen erklärt, mit Beispiel und dem Fehler, der am häufigsten passiert.';

    /**
     * @return array<string,array{
     *     begriff:string, kurz:string, warum:string, beispiel:string, fehler:string,
     *     sartu:string, verwandt:list<string>, ziel:array{0:string,1:string}
     * }>
     */
    public static function alle(): array
    {
        return [
            'firmenwebsite' => [
                'begriff' => 'Firmenwebsite',
                'kurz' => 'Eine Firmenwebsite ist die eigene Seite eines Unternehmens unter '
                    . 'eigener Adresse. Sie erklärt das Angebot, nennt den Kontaktweg und '
                    . 'gehört dem Betrieb — anders als ein Eintrag in einem Branchenverzeichnis.',
                'warum' => 'Sie ist die einzige Stelle im Netz, an der ein Betrieb selbst '
                    . 'bestimmt, was über ihn steht. Alles andere — Verzeichnisse, Portale, '
                    . 'Bewertungsseiten — gehört jemand anderem.',
                'beispiel' => 'Ein Malerbetrieb mit acht Beschäftigten hat sechs Seiten: '
                    . 'Startseite, Innenanstrich, Fassade, Wärmedämmung, Kontakt und '
                    . 'Impressum.',
                'fehler' => 'Die Seite beschreibt den Betrieb statt sein Angebot. Wer nach '
                    . '„Fassade streichen" sucht, findet eine Seite über die Firmengeschichte.',
                'sartu' => 'Wir leiten die Seitenstruktur aus dem ab, wonach in Ihrem Fach '
                    . 'gesucht wird — nicht aus dem Organigramm.',
                'verwandt' => ['Relaunch', 'Local SEO', 'Festpreis'],
                'ziel' => ['/leistung-webdesign', 'Webdesign'],
            ],

            'festpreis' => [
                'begriff' => 'Festpreis',
                'kurz' => 'Ein Festpreis ist ein vorher genannter Gesamtbetrag für einen '
                    . 'vorher festgelegten Umfang. Er ändert sich nicht mit dem Aufwand. '
                    . 'Weicht der Umfang ab, gibt es dafür ein eigenes Angebot.',
                'warum' => 'Ohne Festpreis kennt niemand den Endbetrag, bevor die Rechnung '
                    . 'kommt. Bei einer Website mit vier- bis fünfstelligem Betrag ist das der '
                    . 'teuerste offene Punkt eines Auftrags.',
                'beispiel' => 'SARTU-Umfang Wachstum: 3.900 € netto einmalig für bis zu acht '
                    . 'Seiten mit rund 3.500 Wörtern und zwei Korrekturrunden.',
                'fehler' => 'Ein Festpreis ohne festgelegten Umfang. Dann ist er keiner — dann '
                    . 'ist er eine Schätzung mit Nachforderung.',
                'sartu' => 'Umfang, Seitenzahl, Wortzahl und Korrekturrunden stehen im Angebot. '
                    . 'Der Preis endet exakt dort.',
                'verwandt' => ['Firmenwebsite', 'Hosting'],
                'ziel' => ['/preise', 'Preise'],
            ],

            'hosting' => [
                'begriff' => 'Hosting',
                'kurz' => 'Hosting ist der Platz auf einem Server, unter dem eine Website '
                    . 'erreichbar ist. Ohne Hosting gibt es die Seite als Datei, aber nicht im '
                    . 'Netz.',
                'warum' => 'Hosting ist eine laufende Leistung, kein einmaliger Kauf. Wer sie '
                    . 'nicht mitbestellt, hat eine fertige Website und keine erreichbare.',
                'beispiel' => 'Bei SARTU steckt Hosting im Rundum-Schutz ab 59 € netto im '
                    . 'Monat — zusammen mit Zertifikat, Sicherungen und Überwachung.',
                'fehler' => 'Hosting beim Anbieter mit dem niedrigsten Preis und niemand, der zuständig ist, '
                    . 'wenn die Seite nicht erreichbar ist. Der Preisunterschied fällt beim '
                    . 'ersten Ausfall nicht mehr ins Gewicht.',
                'sartu' => 'Wir betreiben die Website selbst, auf Servern in der EU, mit '
                    . 'täglichen Sicherungen.',
                'verwandt' => ['Domain', 'Festpreis'],
                'ziel' => ['/leistung-wartung', 'Rundum-Schutz'],
            ],

            'domain' => [
                'begriff' => 'Domain',
                'kurz' => 'Eine Domain ist die Adresse, unter der eine Website erreichbar ist — '
                    . 'zum Beispiel `ihr-betrieb.de`. Sie wird gemietet, nicht gekauft, und '
                    . 'gehört demjenigen, der als Inhaber eingetragen ist.',
                'warum' => 'An der Domain hängen auch Ihre E-Mail-Adressen. Wer sie ohne '
                    . 'Vorbereitung umzieht, schaltet damit die Firmenpost ab.',
                'beispiel' => 'Ein Betrieb wechselt den Websitedienstleister. Die Domain bleibt '
                    . 'dieselbe, nur der Eintrag, auf welchen Server sie zeigt, ändert sich.',
                'fehler' => 'Die Domain ist auf den früheren Dienstleister eingetragen. Beim '
                    . 'Wechsel muss der Betrieb ihn um Herausgabe bitten — und manchmal ist er '
                    . 'nicht mehr erreichbar.',
                'sartu' => 'Inhaber bleiben Sie, auch wenn wir die Domain technisch verwalten. '
                    . 'Vor jeder Umstellung sichern wir Ihre bestehenden E-Mail-Einträge.',
                'verwandt' => ['Hosting', 'Relaunch'],
                'ziel' => ['/leistung-wartung', 'Rundum-Schutz'],
            ],

            'relaunch' => [
                'begriff' => 'Relaunch',
                'kurz' => 'Ein Relaunch ist der Neubau einer vorhandenen Website. Adresse und '
                    . 'oft auch Inhalte bleiben, Struktur, Gestaltung und Technik entstehen neu.',
                'warum' => 'Bei einem Relaunch entscheidet sich, ob die Seite ihre Auffindbarkeit '
                    . 'behält. Wer alte Adressen ersatzlos abschaltet, verliert jeden Verweis, '
                    . 'der darauf zeigt.',
                'beispiel' => 'Aus `betrieb.de/leistungen.html` wird `betrieb.de/fassade`. Die '
                    . 'alte Adresse leitet dauerhaft auf die neue weiter.',
                'fehler' => 'Neue Seite, neue Adressen, keine Weiterleitungen. Die Seite '
                    . 'verschwindet für einige Wochen aus den Ergebnissen — und manchmal länger.',
                'sartu' => 'Wir nehmen die alten Adressen auf und leiten jede einzeln weiter, '
                    . 'bevor die neue Seite live geht.',
                'verwandt' => ['Domain', 'Local SEO'],
                'ziel' => ['/leistung-webdesign', 'Webdesign'],
            ],

            'barrierefreiheit' => [
                'begriff' => 'Barrierefreiheit',
                'kurz' => 'Barrierefreiheit heißt, dass eine Website auch mit Tastatur, '
                    . 'Vorlesesoftware oder bei geringem Sehvermögen bedienbar ist. Dazu '
                    . 'gehören Kontrast, Beschriftungen und eine sinnvolle Reihenfolge.',
                'warum' => 'Seit dem Barrierefreiheitsstärkungsgesetz gilt sie für '
                    . 'Verbraucherangebote im elektronischen Geschäftsverkehr. Zwei Fragen '
                    . 'entscheiden, ob Ihr Betrieb darunter fällt: Schließen Besucher über die '
                    . 'Seite einen Vertrag ab? Und wie groß ist der Betrieb?',
                'beispiel' => 'Ein Kontaktformular, dessen Felder beschriftet sind und das sich '
                    . 'ohne Maus ausfüllen lässt.',
                'fehler' => 'Grau auf Hellgrau, weil es ruhig aussieht. Der Text ist für einen '
                    . 'Teil der Besucher nicht lesbar, und niemand meldet es — sie gehen.',
                'sartu' => 'Kontrast, Tastaturbedienung und sichtbarer Fokus sind bei jeder '
                    . 'SARTU-Seite Grundausstattung. Die zwei Fragen oben stellen wir vor dem '
                    . 'Angebot. Eine Rechtsauskunft ist das nicht.',
                'verwandt' => ['Firmenwebsite'],
                'ziel' => ['/leistung-webdesign', 'Webdesign'],
            ],

            'local-seo' => [
                'begriff' => 'Local SEO',
                'kurz' => 'Local SEO ist die Arbeit daran, dass ein Betrieb bei Suchanfragen '
                    . 'mit örtlichem Bezug erscheint. Dazu gehören gleiche Unternehmensdaten '
                    . 'überall, ein gepflegtes Unternehmensprofil und Seiten mit echtem '
                    . 'Ortsbezug.',
                'warum' => 'Wer einen Handwerker sucht, sucht meist in seiner Nähe. Der '
                    . 'Kartenbereich steht dabei über den normalen Ergebnissen.',
                'beispiel' => 'Name, Anschrift und Telefonnummer stehen auf der Website, im '
                    . 'Unternehmensprofil und in jedem Verzeichnis in derselben Schreibweise.',
                'fehler' => 'Zwanzig Seiten, auf denen nur der Stadtname getauscht ist. Google '
                    . 'nennt das in seinen Spam-Richtlinien ausdrücklich und wertet es ab.',
                'sartu' => 'Wir sorgen für gleiche Unternehmensdaten und bauen eine Ortsseite '
                    . 'nur dann, wenn sich über den Ort etwas Eigenes schreiben lässt.',
                'verwandt' => ['GEO (KI-Suche)', 'Firmenwebsite'],
                'ziel' => ['/leistung-seo-lokal', 'Sichtbarkeit'],
            ],

            'geo-ki-suche' => [
                'begriff' => 'GEO (KI-Suche)',
                'kurz' => 'GEO steht für Generative Engine Optimization — die Frage, ob ein '
                    . 'Betrieb in den Antworten von KI-Systemen vorkommt. Eine eigene '
                    . 'Auszeichnung dafür gibt es nicht.',
                'warum' => 'Immer mehr Suchen enden in einer Antwort statt in einer Liste. Wer '
                    . 'dort nicht vorkommt, wird nicht geklickt.',
                'beispiel' => 'Auf die Frage „Was kostet eine Firmenwebsite?" nennt ein '
                    . 'KI-System Zahlen — und die stammen von Seiten, auf denen Zahlen stehen.',
                'fehler' => 'Ein „GEO-Paket" kaufen. Google sagt selbst: „There are no '
                    . 'additional requirements to appear in AI Overviews or AI Mode, nor other '
                    . 'special optimizations necessary."',
                'sartu' => 'Wir schreiben nachprüfbare Fakten als Text — Preise, Umfänge, '
                    . 'Grenzen. Die einzige Untersuchung dazu (Aggarwal u. a., KDD 2024) findet '
                    . 'genau drei wirksame Verfahren: Zahlen statt Beschreibungen, Zitate und '
                    . 'genannte Quellen.',
                'verwandt' => ['Local SEO', 'Firmenwebsite'],
                'ziel' => ['/leistung-seo-lokal', 'Sichtbarkeit'],
            ],
        ];
    }

    /** @return array<string,mixed>|null */
    public static function finden(string $schluessel): ?array
    {
        return self::alle()[$schluessel] ?? null;
    }
}
