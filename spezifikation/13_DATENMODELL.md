# Datenmodell, Formate und Konventionen

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Zusammengeführt am 03.08.2026 aus: `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` §4, §4a
> Wegweiser: `spezifikation/00_UEBERSICHT.md`

> Migrationen, Sicherheit und Architektur: `14_SICHERHEIT.md`.

---

## Die zwanzig Fachtabellen

`schema_migrations` zählt **nicht** mit — es entsteht **vor** allen Fachtabellen.

| Stufe | Tabellen | Testfälle |
|---|---|---:|
| **A0** | `organizations` `users` `sessions` `audit_events` `operator_settings` `legal_texts` | 26 |
| **A1** | `leads` `login_tokens` `projects` `offers` | 34 |
| **A2** | `invoices` `tasks` `task_files` `approvals` `support_messages` | 21 |
| **A3** | `feedback_rounds` `feedback_items` `domain_status` | 6 |
| **B** | `business_hours` `business_hours_exceptions` | 1 |
| **C** | — | 0 |

**Projekte je Organisation:** In Stufe 0 hat eine Organisation **genau ein aktives Projekt**.
Mehrere sind technisch möglich; die Oberfläche zeigt immer das jüngste.

---

## Typabbildung — verbindlich

Zielsystem ist **MySQL 8 / MariaDB 10.6+**. Frühere Fassungen nannten PostgreSQL-Typen; sie
stammen aus dem abgelösten Stack und sind **ungültig**.

| Gemeint | In MySQL / MariaDB | Warum |
|---|---|---|
| Schlüssel | `CHAR(36) CHARACTER SET ascii COLLATE ascii_bin` | MySQL 8 hat keinen UUID-Typ. `ascii` spart je Schlüssel 108 Byte und hält Indizes schmal |
| Zeitpunkt | `DATETIME` | siehe Zeitzonenregel |
| Text ohne Beachtung der Schreibweise | `VARCHAR(n)` mit `utf8mb4_unicode_ci` | vergleicht ohnehin schreibweise-unabhängig — damit wirkt `UNIQUE` auf E-Mail |
| Freitext ohne Index | `TEXT` | |
| Binärdaten | `VARBINARY(n)` | |
| Strukturierte Ablage | `JSON` | MySQL 8 nativ, MariaDB geprüfter Textwert — derselbe Ausdruck |
| IP-Adresse | `VARCHAR(45)` | fasst die längste IPv6-Schreibweise. Bewusst Text, weil das Feld nach 30 Tagen geleert und nie berechnet wird |
| Wahrheitswert | `TINYINT(1)` | |
| `now()` | `CURRENT_TIMESTAMP` | |

**Tabellenvorgabe:** `ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci`

---

## Die Regeln, an denen es sonst still schiefgeht

- **`id CHAR(36) ascii_bin`, in PHP erzeugt**, nicht in der Datenbank
- **`DATETIME` speichert keine Zeitzone.** Alle Zeitpunkte in **UTC**. Nach **jedem**
  Verbindungsaufbau `SET time_zone = '+00:00'` — ohne diese Zeile landen Vorgabewerte in der
  Serverzeit
- Anzeige in **Europe/Berlin**, umgerechnet in PHP
- Fremdschlüssel **`ON DELETE RESTRICT`**. **Keine harte Löschung** fachlicher Daten — `archived_at`
- Geld als **integer in Cent**
- **`NOT NULL` erlaubt `''`.** Für jedes Pflichtfeld zusätzlich serverseitig: nach `trim()`
  mindestens ein Zeichen. **`CHECK (x IS NOT NULL OR y IS NOT NULL)` ist wirkungslos**
- Admins haben **keine** `organization_id` — ein `CHECK` auf `users` erzwingt das

---

## Formate in der Oberfläche

| Thema | Festlegung |
|---|---|
| **Sprache** | `<html lang="de">`, durchgehend deutsch, keine Umschaltung |
| **Zeitzone** | **Europe/Berlin** für jede Anzeige, Speicherung UTC |
| **Datum** | `TT.MM.JJJJ` — z. B. `04.08.2026`. **Nie ISO in der Oberfläche** |
| **Datum mit Uhrzeit** | `TT.MM.JJJJ, HH:MM Uhr` |
| **Wochentage** | ausgeschrieben, Woche beginnt **Montag** |
| **Geldbeträge** | Speicherung integer in Cent. Anzeige `1.234,50 €` |
| **Umsatzsteuer** | **19 %**. `vat_cents = round(net_cents * 0.19)`, `gross_cents = net_cents + vat_cents`. **Der Satz liegt als Konstante im Code, nicht verstreut** |
| **Preisangaben** | öffentlich **netto**, jede Anzeige mit `zzgl. gesetzlicher Umsatzsteuer` |
| **Zahlungsplan** | fest `50_50` und `40_30_30`. **Ausnahme Sonderprojekt:** `custom` |
| **Zahlungsziel** | **10 Kalendertage** ab Rechnungsdatum als Vorbelegung für `due_date` |
| **Dateigrößen** | `12,4 MB` — deutsch, eine Nachkommastelle |
| **Nummernkreise** | Angebot `AN-JJJJ-NNN`, Rechnung `RE-JJJJ-NNN`, je Jahr fortlaufend. In Stufe 0 vom Admin eingegeben, **Eindeutigkeit erzwingt die Datenbank** |
| **Telefonnummern** | Anzeige wie eingegeben, **keine** automatische Umformatierung |
| **Leere Werte** | nie `null`, `–` oder `undefined`. Stattdessen **`Noch nicht hinterlegt`** |

### Zahlungsplan `custom` — nur Sonderprojekt

Der Admin trägt die Raten als **Klartext** in `payment_plan_custom` ein, eine Rate je Zeile:
`Bezeichnung | Betrag netto | Fälligkeit`

```
Anzahlung bei Auftragsbestätigung | 5.000,00 € | sofort
Zwischenrate bei Vorschau         | 5.000,00 € | bei Freigabe der Vorschau
Schlussrate bei Veröffentlichung  | 2.500,00 € | bei Veröffentlichung
```

**Das Portal rechnet daraus nichts ab.** Es zeigt den Text an, der Admin legt die Rechnungen
manuell an.

**Prüfregel:** Die Summe der Raten muss `one_time_net_cents` entsprechen, sonst:
> Die Summe der Raten ergibt {Summe} € und passt nicht zum Einmalpreis von {Einmalpreis} €.

---

## Statuswerte

Bezeichner im Datenmodell sind **englisch** (`organizations`, `users`), Statuswerte und
`operator_settings` **deutsch** (`angebot_offen`, `firmenname`). **Das ist so vorgegeben und wird
nicht vereinheitlicht.**

**Der Kunde sieht nie einen Systemcode** wie `qa_failed`, immer Klartext — `08_TEXTREGELN.md`.
