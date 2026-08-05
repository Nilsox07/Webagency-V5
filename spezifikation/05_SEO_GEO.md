# SEO und GEO — die Kundenleistung

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Zusammengeführt am 03.08.2026 aus: Masterkonzept §16, `GEO_DISCOVERY_CHECKLIST.md`
> Wegweiser: `spezifikation/00_UEBERSICHT.md`

> **Hier steht, was der Kunde kauft.** Die SEO- und GEO-Struktur der **eigenen** SARTU-Website
> steht in `16_SEO_GEO_SARTU.md`. Diese Trennung ist der Grund, warum es diese Dateien gibt —
> sie war in den alten Unterlagen angelegt, aber nirgends durchgezogen.

---

## SEO- und GEO-Startsystem — im Websitepreis, ab Launch

> **Grundhaltung, belegt durch Google-Doku:** GEO ist **kein** magischer Zusatz und **kein**
> Spezial-Schema. Gute KI-Sichtbarkeit ist die Fortsetzung guter SEO: crawlbare, hilfreiche,
> konsistente, entitätsklare Inhalte. **Keine** Garantie auf Rankings, Anfragen, Umsatz oder
> KI-Nennungen. `llms.txt` wird angelegt, aber **nicht** als Rankingfaktor beworben.

**Enthalten ab Launch:** Suchintention und Thema je Seite · **Antwort-zuerst-Texte aus
bestätigten Fakten** · sprechende URLs (Bindestriche, keine Umlaute) · genau **eine** H1 ·
interne Links als echte Links · Title/Description/Canonical/OG/Robots · Breadcrumb +
`BreadcrumbList` · `Organization` + `WebSite` global, `Service`/`Article`/`DefinedTerm`
seitenweise **nur bei sichtbarer Entsprechung** · XML-Sitemap, robots.txt, 404, Redirect-Plan ·
echte NAP, `LocalBusiness` **nur** bei berechtigtem Standort · Bild-SEO · Search Console + Bing
Webmaster + Sitemap eingereicht, IndexNow optional.

**Performance-Gate vor Livegang, im Labor:** LCP < 2,5 s · TBT < 200 ms · CLS < 0,1.
Echte Core Web Vitals inklusive INP erst als **Feldmessung nach** Livegang.
AVIF/WebP + srcset · Hero nicht lazy, `fetchpriority=high` · self-hosted WOFF2 mit
`font-display:swap`.

**`FAQPage`** ist optional — seit Juni 2026 ohne Rich Results.

**Später:** Sichtbarkeitsausbau als **ein** datenbasiertes Folgeangebot — schwache Seiten anhand
echter Suchanfragen verbessern, veraltete Aussagen aktualisieren, interne Verlinkung schärfen.
**Kein SEO-Menü, keine Stufen, keine Minuten.**

---

## Technische Auffindbarkeit für KI-Antworten

Aus `GEO_DISCOVERY_CHECKLIST.md`. Die Punkte sind **Voraussetzungen**, keine Rankinghebel:

- Die Seite ist **crawlbar** und liefert Inhalt ohne JavaScript-Ausführung
- **Eine Quelle für alles:** Fußbereich, Impressum, Rechnungen, E-Mails und strukturierte Daten
  ziehen dieselben Werte. **Uneinheitliche Angaben führen dazu, dass KI-Antworten gar nichts
  zuordnen**
- Entitätsklarheit: Name, Ort, Leistung eindeutig und wiederholt konsistent
- `llms.txt` wird angelegt, **nie** als Rankingfaktor beworben

## Was ausdrücklich nicht gebaut wird

Es gibt **kein** GEO-Schema, **keine** KI-Textdatei, **keine** Sonderauszeichnung. Google Search
Central dazu wörtlich:

> *„There are no additional requirements to appear in AI Overviews or AI Mode, nor other special
> optimizations necessary."*

Googles eigene Anleitung rät zusätzlich ab von: Texten, die Fragevarianten abdecken sollen
(*„Focus on user satisfaction, not query variations"*) und vom Zerstückeln in Häppchen
(*„There's no requirement to break your content into tiny pieces"*). Die kursierende Empfehlung,
auf 134–167 Wörter je Passage zu optimieren, stammt von Anbietern, nicht von Google.

**Strukturierte Daten sind nützlich, aber kein Hebel für KI-Antworten.**

## Was messbar wirkt

Aggarwal u. a., *GEO: Generative Engine Optimization*, ACM KDD 2024 (arXiv:2311.09735) — neun
Verfahren an rund 10.000 Anfragen getestet:

| Verfahren | Wirkung |
|---|---|
| **Zitate einbauen** | bester Wert |
| **Zahlen statt qualitativer Beschreibung** | zweitbester |
| **Quellen nennen** | drittbester |
