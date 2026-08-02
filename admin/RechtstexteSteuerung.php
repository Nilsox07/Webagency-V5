<?php

declare(strict_types=1);

namespace Sartu\Admin;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\AuditProtokoll;
use Sartu\Data\RechtstexteSpeicher;
use Sartu\Helpers\Http;

/**
 * Rechtstexte mit Freigabezustand — Portal-Lastenheft §1.4a.
 *
 * Hier entsteht kein Textinhalt. `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §2 hat die Rechtstexte auf
 * „offen" stehen: Entwuerfe werden geschrieben und anschliessend anwaltlich geprueft. Diese
 * Oberflaeche verwaltet den Zustand, sie formuliert nichts.
 *
 * Den Zustand auf `freigegeben` setzt nur ein Mensch, mit Datum und Namen der pruefenden
 * Stelle. Kein automatischer Uebergang, keine Voreinstellung.
 */
final class RechtstexteSteuerung
{
    private const ZUSTAENDE = [
        'entwurf'     => 'Entwurf',
        'in_pruefung' => 'in Prüfung',
        'freigegeben' => 'freigegeben',
    ];

    public function __construct(private readonly ?RechtstexteSpeicher $speicher = null)
    {
    }

    /** @param array<string,string> $parameter */
    public function liste(array $parameter = []): Antwort
    {
        $texte = [];
        foreach (array_keys(RechtstexteSpeicher::BESCHRIFTUNGEN) as $slug) {
            $texte[$slug] = $this->speicher()->intern($slug);
        }

        return Antwort::html(Ansicht::seite('admin', 'admin-rechtstexte', [
            'titel'          => 'Rechtstexte',
            'angemeldet'     => true,
            'texte'          => $texte,
            'beschriftungen' => RechtstexteSpeicher::BESCHRIFTUNGEN,
            'zustaende'      => self::ZUSTAENDE,
        ]));
    }

    /**
     * @param array<string,string> $parameter
     * @param list<string> $fehler
     * @param list<string> $hinweise
     */
    public function einzeln(array $parameter = [], array $fehler = [], array $hinweise = []): Antwort
    {
        $slug = $parameter['slug'] ?? '';

        if (!isset(RechtstexteSpeicher::BESCHRIFTUNGEN[$slug])) {
            return $this->nichtGefunden();
        }

        return Antwort::html(Ansicht::seite('admin', 'admin-rechtstext', [
            'titel'         => RechtstexteSpeicher::BESCHRIFTUNGEN[$slug],
            'angemeldet'    => true,
            'slug'          => $slug,
            'beschriftung'  => RechtstexteSpeicher::BESCHRIFTUNGEN[$slug],
            'text'          => $this->speicher()->intern($slug),
            'fehler'        => $fehler,
            'hinweise'      => $hinweise,
            'zustaende'     => self::ZUSTAENDE,
        ]));
    }

    /** @param array<string,string> $parameter */
    public function speichern(array $parameter = []): Antwort
    {
        $slug = $parameter['slug'] ?? '';
        $nachweis = AdminNachweis::ausSitzung();

        if (!isset(RechtstexteSpeicher::BESCHRIFTUNGEN[$slug]) || $nachweis === null) {
            return $this->nichtGefunden();
        }

        $rumpf = trim(Http::eingabe('body') ?? '');

        if ($rumpf === '') {
            return $this->einzeln($parameter, ['Der Text ist leer. Speichern Sie erst, wenn etwas darin steht.']);
        }

        $vorher = $this->speicher()->intern($slug);

        if ($vorher === null) {
            $this->speicher()->anlegen($slug, $rumpf, RechtstexteSpeicher::ZIELGRUPPEN[$slug]);
        } else {
            $this->speicher()->entwurfSpeichern($slug, $rumpf);
        }

        (new AuditProtokoll())->schreiben(
            aktion: 'rechtstext_geaendert',
            objektart: 'legal_texts',
            akteurBenutzerId: $nachweis->adminBenutzerId,
            alterWert: $vorher === null ? null : (string) $vorher['status'],
            neuerWert: 'entwurf',
            grund: 'Entwurf gespeichert',
            detail: ['slug' => $slug],
            ip: Http::gegenstelle(),
        );

        return $this->einzeln($parameter, [], ['Der Entwurf ist gespeichert.']);
    }

    /** @param array<string,string> $parameter */
    public function freigabe(array $parameter = []): Antwort
    {
        $slug = $parameter['slug'] ?? '';
        $nachweis = AdminNachweis::ausSitzung();

        if (!isset(RechtstexteSpeicher::BESCHRIFTUNGEN[$slug]) || $nachweis === null) {
            return $this->nichtGefunden();
        }

        $vorher = $this->speicher()->intern($slug);

        if ($vorher === null) {
            return $this->einzeln($parameter, ['Für diesen Text gibt es noch keinen Entwurf.']);
        }

        $zustand = Http::getrimmteEingabe('zustand');
        $stelle = Http::getrimmteEingabe('geprueft_von');
        $grund = Http::getrimmteEingabe('grund');

        if ($grund === '') {
            return $this->einzeln($parameter, ['Tragen Sie einen Grund ein. Er steht später im Protokoll.']);
        }

        try {
            $this->speicher()->zustandSetzen($slug, $zustand, $stelle === '' ? null : $stelle);
        } catch (\InvalidArgumentException) {
            return $this->einzeln($parameter, [
                'Für eine Freigabe brauchen wir den Namen der prüfenden Stelle.',
            ]);
        }

        (new AuditProtokoll())->schreiben(
            aktion: 'rechtstext_zustand_geaendert',
            objektart: 'legal_texts',
            akteurBenutzerId: $nachweis->adminBenutzerId,
            alterWert: (string) $vorher['status'],
            neuerWert: $zustand,
            grund: $grund,
            detail: ['slug' => $slug, 'geprueft_von' => $stelle],
            ip: Http::gegenstelle(),
        );

        return $this->einzeln($parameter, [], ['Der Zustand ist gesetzt.']);
    }

    private function speicher(): RechtstexteSpeicher
    {
        return $this->speicher ?? new RechtstexteSpeicher();
    }

    private function nichtGefunden(): Antwort
    {
        return Antwort::html(Ansicht::seite('oeffentlich', 'fehler', [
            'titel'   => 'Diese Seite gibt es nicht',
            'meldung' => 'Der Link führt ins Leere. Vielleicht hat sich die Adresse geändert.',
            'kennung' => null,
        ]), 404);
    }
}
