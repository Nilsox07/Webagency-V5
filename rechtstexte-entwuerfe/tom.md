ENTWURF — NICHT GEPRÜFT, NICHT VERÖFFENTLICHEN

# Technische und organisatorische Maßnahmen (Art. 32 DSGVO)

*Anlage zum Auftragsverarbeitungsvertrag. Die Maßnahmen unten sind **im Code umgesetzt und
im Test nachgewiesen** — sie sind der belastbare Teil dieses Entwurfs. Die Abschnitte, die
Hardware, Räume und Betriebsabläufe betreffen, sind es nicht.*

## Vertraulichkeit

**Zugangskontrolle**
- Adminanmeldung mit Passwort **und** zweitem Faktor (TOTP nach RFC 6238). Auch lokal nicht abschaltbar
- Passwörter mit Argon2id gespeichert
- Kunden melden sich ohne Passwort an; gespeichert wird nur der Hashwert des Anmeldelinks. Er gilt 15 Minuten und lässt sich einmal verwenden
- Höchstens 5 Anmeldeversuche je E-Mail-Adresse und Stunde
- Anmeldungen gelten serverseitig: eine gelöschte Sitzung wirkt sofort, nicht erst mit dem Cookie

**Zugriffskontrolle**
- Kunden- und Adminzugriff sind getrennte Zugriffsschichten mit getrenntem Code
- Jede Kundenabfrage filtert nach der Organisation **aus der Sitzung**, nie aus einem Request-Wert
- Ein Objekt einer fremden Organisation antwortet mit 404, nicht mit 403 — 403 verriete die Existenz
- Nachgewiesen in `tests/TenantIsolationTest.php` über die **vollständige** Routenliste

**Verschlüsselung**
- Verschlüsselte Felder mit AES-256-GCM über `sodium_*`
- Übertragung ausschließlich über TLS; Sicherheitsheader in jeder Antwort, HSTS in Produktion

## Integrität

- Alle Datenbankzugriffe über vorbereitete Anweisungen, nachgewiesen in `tests/PreparedStatementsTest.php`
- CSRF-Token bei jedem POST, zentral erzwungen
- Uploads liegen außerhalb des Webroots; ausgeliefert wird nur über eine Route, die Sitzung und Organisation prüft
- Dateityp wird am Inhalt geprüft, nicht an der Endung
- Keine harte Löschung fachlicher Daten
- Protokolleinträge lassen sich weder ändern noch löschen — durch Datenbanktrigger erzwungen

## Verfügbarkeit

- Tägliche Sicherungen *(vom Hoster zu bestätigen)*
- Migrationen nur über die Befehlszeile und nur mit angegebener Sicherungsdatei
- Während einer Migration antworten Kunden- und Adminbereich mit 503

## Belastbarkeit und Überprüfung

- Protokollierung von Anmeldung, fehlgeschlagener Anmeldung, Status- und Zahlungswechsel, Rechteänderung und Löschung — je mit handelndem Benutzer, Zeitpunkt und IP-Adresse
- Automatisierte Testabdeckung der Sicherheitsregeln

## Was hier fehlt und beim Hoster liegt

Zutrittskontrolle zum Rechenzentrum · Trennung von Produktiv- und Testsystem auf der Hardware ·
Wiederherstellungszeiten · Auftragsverarbeitungsvertrag mit dem Hoster · Verzeichnis der
Verarbeitungstätigkeiten · Verpflichtung der Beschäftigten auf Vertraulichkeit.

**Ohne diese Angaben ist die Anlage unvollständig.** Sie hängen an
`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4.
