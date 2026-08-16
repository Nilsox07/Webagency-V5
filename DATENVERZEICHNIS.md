# Datenverzeichnis — was die Anwendung tatsächlich speichert

**Stand:** 16.08.2026 · **Quelle:** das Schema aus `migrations/001` bis `040` und die Stellen im
Code, die schreiben. **Nicht** aus einer Vorlage abgeschrieben.

> **Das ist kein Rechtstext und keine Datenschutzerklärung.** Es ist die technische Grundlage,
> aus der eine qualifizierte Stelle beides erstellen kann. Wer aus dieser Datei einen
> veröffentlichungsfähigen Text macht, muss ihn prüfen lassen — `SARTU_ENTSCHEIDUNGEN_OFFEN.md`
> §2 gilt unverändert. **Kein Modell setzt `freigegeben`.**
>
> **Warum es diese Datei überhaupt gibt:** Eine Datenschutzerklärung, die aus einem Muster
> stammt, beschreibt eine andere Anwendung als die, die läuft. Der einzige Weg zu einem Text,
> der stimmt, führt über eine Aufstellung, die aus dem Schema kommt. Diese hier.

---

## 1. Wovon nichts gespeichert wird

Diese Zeile steht zuerst, weil sie das meiste erklärt:

| | |
|---|---|
| **Analysedienst** | keiner. Kein Google Analytics, kein Matomo, kein Pixel |
| **Externe Ressource** | keine. Keine Schriftdatei, kein CDN, kein eingebettetes Video, keine Karte |
| **Cookie auf Marketing-, Redaktions- und Rechtsseiten** | keines. Nachgemessen von `AuslieferungTest::testKeineNormaleOeffentlicheSeiteSetztEinCookie` über **alle** öffentlichen GET-Routen aus `app/routes.php` |
| **Seitenübergreifende Erstkontakt-Zuordnung** | aufgegeben am 16.08.2026. Die Herkunft wird erst beim Einstieg in `/briefing` erfasst — siehe Abschnitt 4 |
| **Anreicherung aus Fremdquellen** | keine. Kein Standortnachschlagen, keine Bonitätsprüfung, keine Bewertung (`spezifikation/09_ANFRAGEEINGANG.md` §4) |
| **Profilbildung, automatisierte Einzelentscheidung** | keine. Die Empfehlung im Bedarfsscheck rechnet aus den Antworten, die der Interessent selbst gegeben hat, und ist ein Vorschlag — verbindlich ist erst das geprüfte Angebot |

Damit ist auch **kein Einwilligungsbanner nötig**, und es gibt keines.

---

## 2. Die Sitzung — wo überhaupt eine entsteht

Seit dem 16.08.2026 entscheidet das nicht mehr eine Ausnahmeliste, sondern die Route selbst
(`Route::$sitzung`, `Router::brauchtSitzung`). Eine Sitzung entsteht **nur**, wenn eine der drei
Bedingungen zutrifft:

| Bedingung | Warum |
|---|---|
| die Route ist mit `sitzung: true` gekennzeichnet | sie zeigt ein CSRF-geschütztes Formular oder trägt Zwischenstand |
| `POST` ohne `ohneCsrf` | ein CSRF-Token braucht einen Speicher |
| Bereich `portal` oder `admin` | dort ist die Anmeldung der Dienst |

**Was daraus folgt, gemessen:**

| Route | Cookie | `Cache-Control` |
|---|---|---|
| `/`, `/preise`, `/leistungen`, `/ablauf`, `/ratgeber`, `/lexikon`, `/foerderung`, `/musterprojekte`, `/ueber-uns`, alle `/leistung-*`, `/ratgeber/{…}`, `/lexikon/{…}` | **keines** | `public, max-age=3600` |
| `/impressum`, `/datenschutz`, `/agb` | **keines** | `public, max-age=3600` |
| `/robots.txt`, `/sitemap.xml`, `/llms.txt` | **keines** | `public, max-age=3600` |
| `/briefing`, `/briefing/1…5`, `/briefing/ergebnis`, `/briefing/kontakt`, `/briefing/danke` | ja | `private, max-age=0, must-revalidate` |
| `/kontakt`, `/website-sanitaer-heizung-klima`, `/website-elektrotechnik`, `/website-dachdecker` | ja | `private, max-age=0, must-revalidate` |
| `/portal/*`, `/admin/*` | ja | `no-store` |

Die drei Branchenseiten tragen ein Cookie, weil sie den Bedarfsscheck als Formular einbetten.
**Das ist eine Bauentscheidung, keine Notwendigkeit:** Wer das Formular dort durch einen Verweis
auf `/briefing` ersetzt, spart drei Cookies. Vermerkt in `OFFENE_PRUEFUNGEN.md`.

**Das Sitzungscookie selbst**, gemessen am 16.08.2026 an `/briefing`:

```
Set-Cookie: PHPSESSID=…; path=/; secure; HttpOnly; SameSite=Lax
```

`Secure` entfällt bei `APP_ENV=local` — ohne TLS würde das Cookie nie gesendet und die
Anmeldung wäre unbenutzbar. Kein `Expires`: Es endet mit dem Browser.
`session.use_strict_mode=1` seit dem 16.08.2026, damit eine von außen vorgegebene Kennung nicht
übernommen wird.

**Eine Antwort, die nicht `200` ist, wird nicht zwischengespeichert** — auch nicht auf einer
cookiefreien Route. `/agb` liefert heute `404`, weil kein Rechtstext freigegeben ist; mit
`public, max-age=3600` hätte ein Zwischenspeicher diese Absage nach der Freigabe bis zu einer
Stunde weitergereicht.

---

## 3. Die Tabellen, in denen Personenbezug steckt

Aufgeführt ist, was **personenbezogen** ist. Reine Sach- und Betragsspalten stehen nicht in der
Liste; sie sind im Schema nachlesbar.

### 3.1 `leads` — Interessenten aus dem Bedarfsscheck

| Feld | Herkunft | Bemerkung |
|---|---|---|
| `first_name`, `last_name`, `company`, `email`, `phone` | Eingabe | `phone` ist freiwillig |
| `preferred_contact` | Eingabe | `email` oder `portal` |
| `payload` | Eingabe | die Antworten des Bedarfsschecks, unverändert. **Ohne** Bestätigungen und Spamabwehr |
| `b2b_confirmed`, `privacy_confirmed` | Eingabe | siehe `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §2a — der Pflichthaken ist eine offene Rechtsfrage |
| `privacy_text_version`, `privacy_confirmed_at` | Server, seit Migration 040 | welcher Datenschutztext und wann |
| `source_ip` | Server | Missbrauchsabwehr. **Nach 30 Tagen geleert**, Datensatz bleibt |
| `self_reported_source` | Eingabe | „Wie sind Sie auf uns gekommen?" — die Antwort des Menschen, keine Messung |
| `landing_page`, `referrer_host`, `utm_*`, `click_id` | Server | siehe Abschnitt 4 |
| `submission_id`, `submitted_at` | Server | gegen Doppeleinreichung |
| `admin_note` | Betreiber | interner Vermerk |
| `delete_after` | Server | das Löschdatum, im Adminbereich sichtbar |

### 3.2 `users` und `organizations` — Kunden

| Feld | Bemerkung |
|---|---|
| `users.email`, `first_name`, `last_name` | ein Benutzer je Kunde (mehrere sind ausdrücklich nicht gebaut) |
| `users.password_hash` | **nur Admins.** Argon2id. Kunden haben kein Passwort, sie melden sich über einen Einmal-Link an |
| `users.totp_secret_enc` | nur Admins, AES-256-GCM über `sodium_*` |
| `users.last_login_at`, `welcome_seen_at` | Zeitstempel |
| `organizations.legal_name`, `brand_name`, `street`, `postal_code`, `city`, `vat_id`, `contact_email`, `contact_phone` | die Firmendaten aus dem Angebot |

### 3.3 Zugang und Nachweis

| Tabelle | Personenbezogene Felder | Aufbewahrung |
|---|---|---|
| `sessions` | `user_id`, `token_hash`, `user_agent`, `ip` | Ablauf 30 Tage; der tägliche Lauf entfernt abgelaufene Zeilen |
| `login_tokens` | `user_id`, `token_hash`, `requested_ip` | Ablauf kurz; der tägliche Lauf entfernt abgelaufene Zeilen |
| `audit_events` | `actor_user_id`, `organization_id`, `ip`, `reason`, `old_value`, `new_value` | **wird nie geändert und nie gelöscht.** Auslöser: Anmeldung, fehlgeschlagene Anmeldung, Status- und Zahlungswechsel, Rechteänderung, Löschung |
| `approvals` | `granted_by_user_id`, `granted_ip`, `granted_name` | Nachweis einer Freigabe |
| `offers` | `accepted_by_user_id`, `accepted_ip`, `accepted_name` | Nachweis einer Annahme |

> **`audit_events` ist die Stelle, an der ein Auskunfts- oder Löschbegehren an eine Grenze
> stößt.** Die Tabelle trägt zwei Trigger aus den Migrationen 005 und 006, die `UPDATE` und
> `DELETE` unterbinden. Das ist gewollt — ein änderbares Protokoll ist keines. Wie sich das zu
> Art. 17 DSGVO verhält, ist eine **Rechtsfrage**, keine technische; sie steht in
> `OFFENE_PRUEFUNGEN.md`.

### 3.4 Inhalte aus der Zusammenarbeit

`tasks`, `task_files`, `feedback_items`, `support_messages`, `approvals` tragen Freitexte und
Dateien des Kunden. `task_files` speichert `original_name`, `stored_name`, `mime_type`,
`size_bytes`, `rights_confirmed` und `uploaded_by_user_id`; die Datei selbst liegt unter
`/storage`, **außerhalb** von `/public`, und wird nur über eine Route mit Mandantenprüfung
ausgeliefert.

### 3.5 Belege

`invoices`, `documents`, `payment_events`, `number_sequences`. `documents` speichert Pfad und
Prüfsumme, `payment_events` die Kennung des Zahlungsereignisses und einen Hash der Nachricht —
**nicht deren Inhalt**. Belege unterliegen der handels- und steuerrechtlichen Aufbewahrung; sie
fallen deshalb **nicht** unter die Löschläufe.

---

## 4. Herkunft einer Anfrage — was seit dem 16.08.2026 anders ist

**Vorher:** `public/index.php` startete auf jeder Anfrage eine Sitzung und legte
Einstiegsseite, Verweisquelle und `utm_*` ab, damit sie beim späteren Absenden noch da waren.
Damit setzte auch das Impressum ein Cookie.

**Warum das nicht bleiben konnte:** § 25 Abs. 2 Nr. 2 TDDDG erlaubt Speichern auf dem Endgerät
ohne Einwilligung nur, wenn es für den vom Nutzer **ausdrücklich gewünschten** Dienst unbedingt
erforderlich ist. Wer das Impressum liest, wünscht das Impressum. Eine Zuordnung, die drei
Seiten später gebraucht wird, ist dafür nicht erforderlich.

**Jetzt:** Erfasst wird beim Einstieg in `/briefing` — dort beginnt der Dienst, den der Nutzer
gewünscht hat. Was daraus folgt, offen benannt:

| | |
|---|---|
| **Was verloren geht** | die Kette „Suchbegriff → Startseite → drei Seiten später Anfrage". Sichtbar ist nur noch, womit der Bedarfsscheck betreten wurde |
| **Was bleibt** | `utm_*` und `click_id` einer Anzeige, die direkt auf `/briefing` oder eine Branchenseite führt, und die Selbstauskunft „Wie sind Sie auf uns gekommen?" |
| **Wer das entscheidet** | entschieden am 16.08.2026 zugunsten der Datensparsamkeit. Wer die Kette zurückwill, braucht eine Einwilligung und damit ein Banner — das ist eine **Geschäftsentscheidung**, keine technische |

---

## 5. Löschung und Aufbewahrung — was wirklich läuft

Alles hier Genannte steht in `bin/cron.php` und ist ausgeführt, nicht geplant.

| Was | Frist | Wo im Code |
|---|---|---|
| `leads.source_ip` leeren | 30 Tage | `Loeschlauf::IP_TAGE`, `AnfrageSpeicher::herkunftsadressenLeeren` |
| Anfrage vollständig löschen | `delete_after` | `Loeschlauf`, `AnfrageSpeicher::faelligeIds` / `endgueltigLoeschen` |
| — abgelehnte Anfragen | 6 Monate | `AnfrageService::FRIST_MONATE_ABGELEHNT` |
| — alle übrigen nicht umgewandelten | 12 Monate | `AnfrageService::FRIST_MONATE_OFFEN` |
| — umgewandelte | **nie automatisch** | Bedingung `converted_organization_id IS NULL` steht in der Abfrage |
| abgelaufene Sitzungen | Ablaufzeitpunkt | `SitzungsSpeicher` |
| abgelaufene Anmelde-Links | Ablaufzeitpunkt | `AnmeldeTokenSpeicher` |
| `audit_events` | **nie** | Trigger in Migration 005 und 006 |
| Belege | handels- und steuerrechtlich | nicht im Löschlauf |

**Betroffenenrechte je Anfrage**, im Adminbereich unter `/admin/anfragen/{id}`:
`Datensatz exportieren` (JSON, alles was gespeichert ist, einschließlich der neuen
Nachweisfelder) und `Endgültig löschen` (echtes `DELETE`, ausdrückliche Ausnahme von der
Archivierungsregel). Der Löschvorgang wird protokolliert — **ohne** die gelöschten Inhalte.

**Protokolle** halten Zeitpunkt, Ergebnis, **gekürzte IP** (letztes Oktett entfernt) und
`submission_id` fest. **Nie** Name, E-Mail, Telefonnummer oder Antworttexte.

---

## 6. Empfänger und Übermittlung

| Empfänger | Wann | Was |
|---|---|---|
| **Mailversand** | bei jeder Benachrichtigung | Empfängeradresse und Nachrichtentext. Lokal fängt Mailpit alles ab; produktiv ist der Anbieter **noch nicht entschieden** — `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4 |
| **Mollie** | beim Bezahlen einer Rechnung | Rechnungsnummer, Betrag, Rückkehradresse. Die Anbindung ruft den Zahlungsstand **selbst ab**; aus einer eingehenden Nachricht wird nie ein Status abgeleitet |
| sonst | — | **keiner.** Kein Analysedienst, kein CDN, kein Schriftanbieter, keine Karte |

Für den Mailversand und für Mollie braucht es je einen Auftragsverarbeitungsvertrag. Beides ist
**offen** und in `OFFENE_PRUEFUNGEN.md` vermerkt.

---

## 7. Was diese Datei nicht leistet

- **Keine Rechtsgrundlagen.** Welche Verarbeitung auf Art. 6 Abs. 1 lit. b, lit. f oder lit. c
  gestützt wird, ist eine juristische Zuordnung. Sie steht hier bewusst nicht, damit sie nicht
  aus einer technischen Datei in einen Rechtstext wandert
- **Keine Fristen für Belege.** Handels- und steuerrechtliche Aufbewahrung gehört in die
  `VERFAHRENSDOKUMENTATION.md`
- **Keine Aussage darüber, ob der heutige Zustand rechtskonform ist.** Diese Datei sagt, was
  passiert. Ob es passieren darf, sagt eine qualifizierte Stelle
