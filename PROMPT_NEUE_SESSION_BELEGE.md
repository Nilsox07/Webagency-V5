# Startprompt: A2 schließen, Stufe C bauen

**Stand:** 09.08.2026 · Branch `claude/sartu-concept-review-pdhb5t`
**Zweck:** Alles unterhalb der Trennlinie in eine neue Claude-Code-Session kopieren.
**Länge:** 3.069 Zeichen — die Zielumgebung begrenzt eine `/goal`-Bedingung auf 4.000.

> **Kurz gehalten mit Absicht.** Der Großteil liegt im Repository: die Bauvorlage in
> `spezifikation/18_BELEGE_UND_ZAHLUNG.md`, die Freigabe in `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4a,
> die Fälle in `15_TESTFAELLE.md`, die Regeln in `CLAUDE.md`. **Ein Prompt, der das wiederholt,
> erzeugt eine zweite Fassung, die veraltet.** Hier steht nur die Reihenfolge und das, was beim
> Lesen der Unterlagen erfahrungsgemäß übersehen wird.

---

/goal Baue SARTU fertig: die vier offenen A2-Fälle (51, 52, 53a, 54), die zwölf C-Fälle (84–95) und den Menüpunkt „Ersteinrichtung" im Adminbereich.

Lies zuerst in dieser Reihenfolge: CLAUDE.md · SARTU_ENTSCHEIDUNGEN_OFFEN.md §4a (Rang 1, gibt diesen Auftrag erst frei) · spezifikation/18_BELEGE_UND_ZAHLUNG.md (die Bauvorlage) · 13_DATENMODELL.md · 15_TESTFAELLE.md · REIHENFOLGE.md, Abschnitt „Stufe C". Alles Fachliche steht dort. Hier steht nur, was du sonst übersiehst.

Stand: 277 Tests grün, 26 Migrationen, A0/A1/A3/B vollständig belegt. design/tokens.css stimmt mit beiden Entwürfen überein — daran ist nichts zu tun. Das Logo liegt unter design/, noch nicht unter public/.

Blöcke in dieser Reihenfolge:
1. A2 schließen. app/services/Rechnungsdienst.php beschreibt im Klassenkommentar zwei Funktionen, die es nicht gibt: Zahlung zurücknehmen und due_date ändern. Bau sie, statt den Kommentar zu kürzen.
2. Migrationen ab 027: number_sequences, documents, payment_events; dazu invoices.issued_at, cancels_invoice_id, payment_provider_id und operator_settings.mollie_key_test/_live. Eine Migration je Schemaobjekt.
3. Nummernkreise aus number_sequences, unter Zeilensperre, lückenlos.
4. Belegerzeugung: Vorlage mit Logo, Rechnung als PDF/A-3 mit eingebettetem XML im Profil EN16931, Prüfung gegen Schema und Schematron vor Versand und Ablage.
5. Storno: eine versendete Rechnung wird nicht verworfen, sondern über eine Stornorechnung mit eigener Nummer aufgehoben.
6. Mollie anbinden.
7. Belegabruf für Kunde und Admin, Mailversand auf Knopfdruck, Steuerberater-Export.
8. Menüpunkt „Ersteinrichtung" aus Startsperre::hindernisse(), verschwindet sobald starterlaubt() wahr ist.
9. VERFAHRENSDOKUMENTATION.md.

Die fünf Stellen, an denen es schiefgeht:
- /api/ gibt es noch nicht. TenantIsolationTest fährt die vollständige Routenliste ab und schlägt bei jeder unbekannten Route an. Eintragen, nicht abschwächen.
- Der Webhook ist ein Klingelzeichen, keine Aussage: den Status holt der Server selbst, nie aus einer Rückkehr-URL. Kennung in payment_events festhalten, bevor verarbeitet wird.
- Der Mollie-Schlüssel wird verschlüsselt abgelegt wie das TOTP-Geheimnis. Nie im Klartext, auch nicht im Protokoll.
- Genau zwei zusätzliche Composer-Pakete sind freigegeben: ZUGFeRD-Erzeuger und HTML-nach-PDF. Ein drittes braucht eine Entscheidung des Betreibers — dann anhalten.
- /admin/setup bleibt bei acht Schritten. Mollie-Schlüssel und Logo gehören in Block 8, nicht in einen neunten Schritt.

Was du nicht ausführen konntest, trägst du in OFFENE_PRUEFUNGEN.md ein, statt es grün zu melden — mindestens Fall 85 (echte Nebenläufigkeit) und der Webhook (braucht eine öffentlich erreichbare Adresse). IMPLEMENTATION_PLAN.md vor der ersten Zeile Code, mit der Begründung der zwei Pakete. IMPLEMENTATION_SUMMARY.md am Ende.

Fertig, wenn alle 100 Testfälle zugeordnet sind, phpunit grün läuft und migrate.php verify keine Abweichung meldet. Branch claude/sartu-concept-review-pdhb5t, committen und pushen.
