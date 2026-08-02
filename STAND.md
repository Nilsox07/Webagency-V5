# Stand

**Eine Seite. Sie sagt, wo der Bau steht — damit ein abgebrochener Lauf dort weitermacht,
wo er aufgehört hat, statt von vorn zu beginnen.**

**Letzte Änderung:** 02.08.2026 · **Zweig:** `claude/php-a0-modellplan-06duus`

---

## Kurz

| | |
|---|---|
| **Fertig** | A0 · A1 · A2 · **A3** |
| **Läuft gerade** | — |
| **Als Nächstes** | **Stufe B**: `business_hours` · `business_hours_exceptions` · die öffentliche Website |
| **Tests** | **218 grün**, 1842 Zusicherungen, gegen echtes MariaDB 11.4. Kein SQLite |
| **Tabellen** | 18 von 20. Es fehlen die zwei aus Stufe B |
| **Testfälle** | **87 von 88** gebaut und geprüft. Offen: der eine aus Stufe B |
| **Migrationen** | 022, lückenlos eingespielt, Prüfsummen stimmen |

---

## Die Etappen im Einzelnen

| Etappe | Tabellen | Testfälle | Zustand |
|---|---|---|---|
| **A0** — Fundament | 6 | 26 | **fertig.** Ersteinrichtung in acht Schritten, Adminanmeldung mit TOTP, Betreiberdaten, Rechtstexte, Testmail, Mandantentrennung |
| **A1** — Anfrage bis Angebot | 4 | 34 | **fertig.** Bedarfsscheck, Anfrageliste, Umwandlung, Kundenanmeldung ohne Passwort, Angebot gesendet, Löschlauf |
| **A2** — Auftrag bis Produktionsstart | 5 | 21 | **fertig.** Annahme, Rechnungen von Hand, Überfälligkeitslauf, zwei Erinnerungen, Aufgaben, Uploads, Faktenfreigabe |
| **A3** — Produktion bis Livegang | 3 | 6 | **fertig.** Vorschau, Korrekturrunden, Abnahme, Domainlage, Onlinegang. **Ein Projekt erreicht `live`** |
| **B** — Öffnungszeiten und die öffentliche Website | 2 | 1 | **offen.** Als Nächstes |
| **C** — Automatik | 0 | 0 | nicht beauftragt |

---

## Zwei Livegänge, nicht einer

`REIHENFOLGE.md`: Der **Pilotkunde** kann nach A3 live gehen. Die **öffentliche Website** geht
erst nach B live — sie darf nur Funktionen bewerben, die es gibt.

| | Wann | Bedingung | Stand |
|---|---|---|---|
| Pilotkunde ist live | nach A3 | Ein echtes Projekt erreicht `live` | **technisch bereit.** Die Strecke läuft im Test von `produktion` bis `live` durch |
| Öffentliche Website geht live | nach B | Nur vorhandene Funktionen bewerben | offen |

---

## Was beim Betreiber liegt — nicht beim Bau

Diese zwei Schritte kann niemand im Code erledigen. Sie stehen hier, damit sie nicht als
vergessen gelten.

### 1. Rechtstexte freigeben

`legal_texts` steht auf `entwurf`. Die **Startsperre (§14a)** verhindert von sich aus, dass
mit einem Entwurf nach außen gegangen wird — sie muss dafür nicht angefasst werden.

> **Ein plausibel klingender Rechtstext ist gefährlicher als gar keiner.** Ein Mensch mit
> juristischer Ausbildung liest ihn, bevor `status` auf `freigegeben` geht. Kein Bauschritt
> setzt diesen Zustand.

Betroffen: Impressum · Datenschutzerklärung · AGB · AVV · TOM.

### 2. Hoster einrichten

Cron und Mail müssen auf echter Hardware laufen; im Container ist beides nur nachgestellt.

- **Cron:** `bin/cron.php` täglich — Überfälligkeit, Zahlungserinnerungen, abgelaufene
  Angebote, Löschfristen. Der Lauf ist ausgeführt, der **Zeitplan** nicht eingerichtet
- **Mail:** SPF, DKIM und DMARC gehören dazu. Mailpit fängt lokal jede Mail ab und sagt über
  Zustellbarkeit nichts
- **TLS:** `session.cookie_secure = 1`, HSTS, und `/admin/setup` über echtes `https://`
- Die vollständige Liste steht in `OFFENE_PRUEFUNGEN.md` unter „Aufgeschoben"

---

## Wenn ein Lauf abbricht — hier weitermachen

1. `docker compose up -d` · falls der Docker-Dienst nicht läuft: `nohup dockerd > /tmp/dockerd.log 2>&1 &`
2. `docker compose exec app vendor/bin/phpunit` — **muss grün sein**, bevor gebaut wird
3. `docker compose exec app php bin/migrate.php status` — eingespielt 22, offen 0
4. `REIHENFOLGE.md` sagt, was jetzt dran ist. `OFFENE_ENTSCHEIDUNGEN.md` sagt, was gemeldet
   und nicht erfunden wurde. `OFFENE_PRUEFUNGEN.md` sagt, was gebaut, aber nicht ausgeführt ist
