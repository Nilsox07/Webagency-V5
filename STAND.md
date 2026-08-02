# Stand

**Eine Seite. Sie sagt, wo der Bau steht — damit ein abgebrochener Lauf dort weitermacht,
wo er aufgehört hat, statt von vorn zu beginnen.**

**Letzte Änderung:** 02.08.2026 · **Zweig:** `main`

---

## Kurz

| | |
|---|---|
| **Fertig** | A0 · A1 · A2 · A3 · **B** |
| **Läuft gerade** | — |
| **Als Nächstes** | **Stufe C** ist nicht beauftragt. Was jetzt ansteht, steht in `LIVEGANG.md` — und zwei Dinge davon kann nur ein Mensch tun |
| **Tests** | **264 grün**, 3508 Zusicherungen, gegen echtes MariaDB 11.4. Kein SQLite |
| **Tabellen** | **20 von 20** |
| **Testfälle** | **88 von 88** gebaut und geprüft |
| **Migrationen** | 025, lückenlos eingespielt, Prüfsummen stimmen |

---

## Was am 02.08.2026 dazukam

| Was | Wo |
|---|---|
| **§4b.6** — das Kontaktformular erzeugt **keinen** Datensatz mehr, nur eine Mail | `Kontaktanfrage`, `WebsiteTest` |
| **§8.1 Block 3** — „Offene Punkte" im Cockpit, mit der entschiedenen Frist von drei Tagen | `PortalSteuerung`, `Zahlungsstatus::fristKnapp()` |
| **Fokusfalle, `Esc` und Klick daneben** im mobilen Menü | `public/assets/js/menue.js`, 2,3 KB, `defer` |
| **`KEYWORD_VALIDATION.md`** — erzeugt aus dem Bau, 32 Adressen | `bin/keywords.php` |
| **`MESSUNGEN.md`** — Kontrast, Tastatur, Antwortzeiten, zwölf Mailwege, beide Bereiche im Browser | `MESSUNGEN.md` |
| **`LIVEGANG.md`** — was vor dem ersten Besucher getan sein muss | `LIVEGANG.md` |
| **`bin/startklar.php`** — die Startsperre als Befehl, Rückgabewert 1 bei jedem Hindernis | `bin/startklar.php` |

**Zwei Fehler hat die Messung gefunden und behoben:** ein Kontrast von 2,05 : 1 auf den drei
Branchenseiten, und ein Menü, das mit `Esc` nicht zuging, obwohl zwei Kommentare das
behaupteten.

**Einen Fehler hat sie gefunden und gemeldet:** Sechs Mails aus §10 gibt es nicht — darunter
`Ihr Angebot von SARTU liegt bereit`. Ohne sie liegt das Angebot im Kundenbereich, und
niemand schickt den Kunden hin. Es steht in `LIVEGANG.md` §6.1 als Sperre vor dem ersten
Kunden.

---

## Die Etappen im Einzelnen

| Etappe | Tabellen | Testfälle | Zustand |
|---|---|---|---|
| **A0** — Fundament | 6 | 26 | **fertig.** Ersteinrichtung in acht Schritten, Adminanmeldung mit TOTP, Betreiberdaten, Rechtstexte, Testmail, Mandantentrennung |
| **A1** — Anfrage bis Angebot | 4 | 34 | **fertig.** Bedarfsscheck, Anfrageliste, Umwandlung, Kundenanmeldung ohne Passwort, Angebot gesendet, Löschlauf |
| **A2** — Auftrag bis Produktionsstart | 5 | 21 | **fertig.** Annahme, Rechnungen von Hand, Überfälligkeitslauf, zwei Erinnerungen, Aufgaben, Uploads, Faktenfreigabe |
| **A3** — Produktion bis Livegang | 3 | 6 | **fertig.** Vorschau, Korrekturrunden, Abnahme, Domainlage, Onlinegang. **Ein Projekt erreicht `live`** |
| **B** — Öffnungszeiten und die öffentliche Website | 2 | 1 | **fertig.** Öffnungszeiten pflegt der Kunde selbst; die öffentliche Website steht mit 30 Adressen, Ratgeber, Lexikon und drei Branchenseiten |
| **C** — Automatik | 0 | 0 | nicht beauftragt |

---

## Zwei Livegänge, nicht einer

`REIHENFOLGE.md`: Der **Pilotkunde** kann nach A3 live gehen. Die **öffentliche Website** geht
erst nach B live — sie darf nur Funktionen bewerben, die es gibt.

| | Wann | Bedingung | Stand |
|---|---|---|---|
| Pilotkunde ist live | nach A3 | Ein echtes Projekt erreicht `live` | **technisch bereit.** Die Strecke läuft im Test von `produktion` bis `live` durch |
| Öffentliche Website geht live | nach B | Nur vorhandene Funktionen bewerben | **technisch bereit.** Die Startsperre §14a hält sie zurück, bis die Rechtstexte freigegeben sind |

---

## Was beim Betreiber liegt — nicht beim Bau

Diese zwei Schritte kann niemand im Code erledigen. Sie stehen hier, damit sie nicht als
vergessen gelten.

### 1. Rechtstexte freigeben

`legal_texts` steht auf `entwurf`. Die **Startsperre (§14a)** verhindert von sich aus, dass
mit einem Entwurf nach außen gegangen wird — sie muss dafür nicht angefasst werden. Solange
sie greift, liefern `/impressum` und `/datenschutz` 404, und `/agb` ist nirgends verlinkt.

> **Ein plausibel klingender Rechtstext ist gefährlicher als gar keiner.** Ein Mensch mit
> juristischer Ausbildung liest ihn, bevor `status` auf `freigegeben` geht. Kein Bauschritt
> setzt diesen Zustand.

Betroffen: Impressum · Datenschutzerklärung · AGB · AVV · TOM.

**Fünf Entwürfe liegen bereit** — in `rechtstexte-entwuerfe/`, jeder mit der Kopfzeile
`ENTWURF — NICHT GEPRÜFT, NICHT VERÖFFENTLICHEN`. Sie liegen als Dateien und **nicht** in der
Datenbank: Ein Entwurf in `legal_texts` ist eine Zeile davon entfernt, freigegeben zu werden.

Jede Anschrift darin steht als `[[PLATZHALTER]]`. Das ist kein Versehen — die Startsperre
sucht genau diese Markierung (§14a Bedingung 1), und die echten Werte stehen in
`operator_settings`.

Der Weg: prüfen lassen → Betreiberdaten eintragen → geprüften Text unter
`/admin/rechtstexte/{slug}` einfügen → **erst dann** freigeben.

### 2. Hoster einrichten

Cron und Mail müssen auf echter Hardware laufen; im Container ist beides nur nachgestellt.

- **Cron:** `bin/cron.php` täglich — Überfälligkeit, Zahlungserinnerungen, abgelaufene
  Angebote, Löschfristen. Der Lauf ist ausgeführt, der **Zeitplan** nicht eingerichtet
- **Mail:** SPF, DKIM und DMARC gehören dazu. Mailpit fängt lokal jede Mail ab und sagt über
  Zustellbarkeit nichts
- **TLS:** `session.cookie_secure = 1`, HSTS, und `/admin/setup` über echtes `https://`
- **Die vollständige Liste steht in `LIVEGANG.md`** — mit Befehlen zum Kopieren und einer
  Prüfliste zum Abhaken. `OFFENE_PRUEFUNGEN.md` sagt zusätzlich, was gebaut, aber nicht
  ausgeführt ist

---

## Wenn ein Lauf abbricht — hier weitermachen

1. `docker compose up -d` · falls der Docker-Dienst nicht läuft: `nohup dockerd > /tmp/dockerd.log 2>&1 &`
2. `docker compose exec app vendor/bin/phpunit` — **muss grün sein**, bevor gebaut wird
3. `docker compose exec app php bin/migrate.php status` — eingespielt 25, offen 0
4. `REIHENFOLGE.md` sagt, was jetzt dran ist. `OFFENE_ENTSCHEIDUNGEN.md` sagt, was gemeldet
   und nicht erfunden wurde. `OFFENE_PRUEFUNGEN.md` sagt, was gebaut, aber nicht ausgeführt ist
5. Vor dem Livegang: `docker compose exec app php bin/startklar.php` und `LIVEGANG.md`
