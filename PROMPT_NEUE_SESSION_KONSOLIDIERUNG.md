# Startprompt: technische Konsolidierung vor der Designrunde

**Stand:** 15.08.2026 · Branch `claude/sartu-concept-review-pdhb5t` · geprüft auf `7fafead`
**Zweck:** Alles unterhalb der Trennlinie in eine neue Claude-Code-Session kopieren.

> **Vor der Gestaltung.** Eine externe Prüfung vom 15.08.2026 hat sechs Punkte gefunden, die
> eine reine Designrunde sinnlos machen würden. Drei davon sind Spezifikationen, die gebaut
> werden sollten, still weggelassen und nie gemeldet wurden.

---

/goal Sechs Punkte technische Konsolidierung. Kein Layout, keine Texte ausser den genannten. Eine externe Prüfung des Stands 7fafead hat sie am 15.08.2026 gefunden; ich habe jeden einzeln nachgemessen.

1 — HTTP-AUSLIEFERUNG
Gemessen: /sitemap.xml, /robots.txt und /llms.txt tragen Content-Type text/html, dazu X-Content-Type-Options nosniff. Eine als HTML ausgelieferte Sitemap liest Google nicht als Sitemap. Ursache: app/Antwort.php Zeile 27, der Operator + behält den linken Schlüssel. Der Kommentar von datei() beschreibt die Falle bereits; Wurzeldateien nutzt die Fabrik nicht.
Ausserdem trägt jede öffentliche Antwort ein PHPSESSID-Cookie und Cache-Control no-store — auch robots.txt. Ursache: public/index.php startet die Sitzung vor der Route.
Bau eigene Antwortfabriken für XML und Plaintext. Starte die Sitzung nur für Bedarfsscheck, Anmeldung, Portal und Admin. Gib öffentlichen GET-Antworten eine sinnvolle Cachepolitik. Schreib Regressionstests für Inhaltstyp, Cookie und Cache-Control je Wurzeldatei.

2 — INSTALLATION REPRODUZIERBAR
setasign/fpdf verlangt laut composer.lock ext-gd; composer.json und LIVEGANG.md nennen es nicht. Ein frisches composer install bricht damit auf jedem Server ohne GD ab, drei Bildtests scheitern. Trag es ein, bring die Lockdatei auf composer validate --strict und ergänze die Livegangliste.

3 — DREI WEGGELASSENE SPEZIFIKATIONEN
Alle drei stehen verbindlich in spezifikation/17_SEITEN_SARTU.md und wurden nie gebaut und nie in OFFENE_PRUEFUNGEN.md gemeldet.
a) Bedarfsscheck Thema 5, Feld 5.2 Logostatus mit vier Optionen: Logo vorhanden als Datei · Logo vorhanden, aber nur gedruckt oder als Foto · Kein Logo · Bin unsicher. Zeile 219. Er ändert den Leistungsweg vor dem Festpreis.
b) Unter der Empfehlung der höchstens zweisätzige Förderhinweis mit Link auf /foerderung und der Reihenfolge Antrag vor Beauftragung. Ohne ihn kann ein Kunde durch zu frühe Beauftragung seinen Anspruch verlieren.
c) /foerderung enthält null Links. app/services/Foerderprogramme.php hat 0 URLs, und die Seite behauptet sichtbar, alle Angaben seien an der Quelle geprüft. FOERDERUNG_KONZEPT.md legt verweisen statt kopieren fest — die Links sind das Konzept, weil sechs von sechzehn Sekundärangaben falsch waren. Trag die 16 Adressen ein; findest du eine nicht, nimm die Behauptung der Quellenprüfung von der Seite.

4 — RANG-1-SPERRE KARRIERE
SARTU_ENTSCHEIDUNGEN_OFFEN.md §7b verbietet bis zur Entscheidung jede Erwähnung einer Karriere- oder Stellenseite auf Website und Branchenseiten. Erwähnt wird sie in Bedarfsscheck, Empfehlung, Empfehlungstext, Musterprojekte, Preisstufen und auf den Branchenseiten. Entfern sie und schreib einen Test, der die Sperre hält — ohne ihn kommt sie zurück.

5 — ORTSNENNUNG NACHZIEHEN
Rang 1 §1 schaltet Ortsnamen in Fliesstext, Fussbereich und /kontakt frei. Der Code begründet ihr Fehlen weiter mit der offenen Anschrift, und WebsiteTest erzwingt kein Ortsname auf der ganzen Website. Stell ihn auf die wirklich gesperrten Stellen um: LocalBusiness, NAP und Ortsseiten ohne Gate. /webdesign-dresden bleibt abwesend, das Gate ist nicht erfüllt.

6 — MOBILES MENÜ
Bei 390 px liegt das Vollbildmenü über Logo und Menütaste. Escape funktioniert, ein sichtbarer Schliessweg für Touch fehlt. Der Aussenklick kann nicht greifen, weil das Overlay selbst innerhalb des details liegt. Setz ein sichtbares X ins Overlay und bau den Aussenklick über eine echte Backdrop-Fläche.

NACHWEIS
Je Punkt eine Messung, nicht eine Behauptung: Kopfzeilen der drei Wurzeldateien, composer validate --strict, ein Bildschirmfoto des Bedarfsscheck-Themas 5, die Zahl der Links auf /foerderung, ein Testlauf der neuen Sperre, ein Touch-Test des Menüs. Was du nicht ausführen konntest, kommt mit je einer Zeile nach OFFENE_PRUEFUNGEN.md — drei weggelassene Spezifikationen ohne Meldung waren der eigentliche Fehler der letzten Runden.

Branch claude/sartu-concept-review-pdhb5t, committen und pushen.
