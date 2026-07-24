# Sartu Template-Auswahl 2026

Stand: 22.07.2026

## Kurzfazit

Das offizielle Tailwind-Plus-Template "Studio" ist als Qualitaetsreferenz sehr gut, aber nicht frei downloadbar. Es kostet laut offizieller Tailwind-Seite aktuell 89 EUR als einzelnes Template oder 249 EUR im Tailwind-Plus-Paket. Deshalb wird es nicht aus inoffiziellen GitHub-Kopien verwendet.

Fuer den ersten Website-Prototyp wird Folex Lite Astro genutzt und stark auf Sartu umgebaut. Es ist fuer eigene kommerzielle Projekte nutzbar, SEO-orientiert, Astro/Tailwind-basiert und bringt mehr Agentur-Struktur mit als sehr simple Landingpages. Fuer spaetere Kunden-Websites sollte es nicht als wiederverkaufbare Theme-Basis dienen, weil die Lizenz keine Weiterverteilung oder Theme-Weiterverkauf erlaubt.

Fuer das Portal wird Studio Admin genutzt. Es ist MIT-lizenziert, wirkt shadcn/ui-nah, bringt eine saubere linke Navigation, Layout-Optionen und fertige Dashboard-/Invoice-/Task-Bausteine mit.

## Website: Pruef- und Optimierungsrunden

### Runde 1: Breite Suche

Geprueft:
- Tailwind Plus Studio
- Folex Lite Astro
- AgenceX Astro
- Astra / Luro AI
- Desgy
- Agency Kit
- Jaume Agency Template
- AstroWind

Ergebnis:
- Tailwind Plus Studio ist offiziell hochwertig, aber kostenpflichtig und ohne Kauf nicht downloadbar.
- Mehrere GitHub-Treffer wirken wie Studio-Nachbauten oder haben keine klare Lizenzdatei. Diese werden verworfen.
- AgenceX ist MIT-lizenziert und technisch schlank, wirkt aber im Demo stark nach Standard-Landingpage mit Gradient-Blobs.
- Folex Lite hat die beste Mischung aus Agentur-Struktur, SEO-Setup, Pricing, Services, Portfolio-Logik und statischer Ausspielung.

### Runde 2: Anti-KI-Optik

Verworfen oder abgewertet:
- Reine Schwarz-Weiss-Layouts ohne eigene Bildwelt.
- Uebermaessige Gradient-Orbs, SaaS-Heroes und generische "Dashboard floating card"-Aesthetik.
- Fake-Kundenlogos, Fake-Testimonials und generische Stock-Business-Fotos.
- Zu viele Feature-Kacheln ohne echte Angebotslogik.

Optimierung fuer Sartu:
- Echter Editorial-Look mit fotografischen Motiven statt abstrakten SVGs.
- Ruhige, aber nicht monochrome Palette: Papierweiss, tiefes Gruen/Ink, warmes Orange und kuehles Blau als Akzente.
- Klarer USP im ersten Screen: Festpreis plus gefuehrtes Portal plus KI-gestuetzte individuelle Umsetzung.
- Wenige Entscheidungen fuer Kunden, aber genug Transparenz ueber Leistungen.

### Runde 3: Umsetzung, SEO/GEO und FTP-Fit

Finale Bewertung:
- Astro ist fuer die Website sinnvoll, weil am Ende statische Dateien erzeugt werden koennen.
- Markdown-/Content-Struktur ist gut fuer spaetere SEO- und GEO-Seiten.
- Folex Lite muss optisch stark angepasst werden, ist aber als Grundgeruest schneller als ein kompletter Neubau.
- Das Portal wird separat als echte Anwendung betrachtet und nicht in die statische Website gemischt.

Finale Website-Wahl:
- Folex Lite Astro als Basis fuer den Sartu-Website-Prototyp.
- AgenceX bleibt Backup, falls die Folex-Lizenz spaeter fuer bestimmte Weiterverwendungsplaene stoert.

## Portal: Pruef- und Optimierungsrunden

### Runde 1: Breite Suche

Geprueft:
- Studio Admin
- TailAdmin Next.js Dashboard
- Bundui shadcn admin dashboard
- Kiranism next-shadcn-dashboard-starter
- Horizon UI Tailwind React

Ergebnis:
- Studio Admin trifft den gewuenschten shadcn/ui-Stil am besten.
- TailAdmin ist solide, wirkt aber klassischer und weniger spezifisch.
- Bundui sieht passend aus, hat lokal aber keine klare Lizenzdatei und wird deshalb nicht verwendet.
- Kiranism ist stark, aber fuer den ersten Portal-Prototyp zu umfangreich.

### Runde 2: Portal-USP statt WordPress-Gefuehl

Anforderungen:
- Kunde soll gefuehrt werden, nicht selbst die Website bauen.
- Sichtbar sein muessen: Projektstatus, Aufgaben, Domain, Zahlungen, Rechnungen, Vorschau, Feedback.
- Aenderungen nur fuer sichere Kleinigkeiten: Oeffnungszeiten, Telefonnummer, Hinweisbanner, Seite pausieren.
- Keine Menues wie "Theme bearbeiten", "Plugins", "Layout Builder" oder "Seite loeschen".

Optimierung:
- Studio Admin wird auf ein Sartu-Kundenportal reduziert.
- Linke Navigation bleibt clean, aber mit Sartu-spezifischen Bereichen.
- Akzentfarben sorgen fuer Wiedererkennung, ohne die Oberflaeche verspielt zu machen.

### Runde 3: Technischer Fit

Finale Bewertung:
- Next.js ist fuer das Portal passend, weil spaeter Login, Mollie, Datenbank, Kundenakte, Adminbereich und Automationen folgen.
- Studio Admin bringt Tabellen, Rechnungsansicht, Tasks, Kalender und responsive Sidebar bereits mit.
- MIT-Lizenz ist klar genug fuer langfristige Weiterentwicklung.

Finale Portal-Wahl:
- Studio Admin als Basis fuer den Sartu-Portal-Prototyp.
- TailAdmin bleibt Backup, falls Studio Admin beim Umbau zu schwer wird.

## Quellen

- Tailwind Plus Studio: https://tailwindcss.com/plus/templates/studio
- Tailwind Plus: https://tailwindcss.com/plus
- Tailwind Blog zu Studio: https://tailwindcss.com/blog/2023-08-07-meet-studio-our-new-agency-template
- Folex Lite Astro: https://github.com/getastrothemes/folex-lite-astro
- AgenceX Astro: https://github.com/uno-forge-hub/agency-landing-page-Astrojs
- Studio Admin: https://github.com/arhamkhnz/next-shadcn-admin-dashboard
- TailAdmin: https://github.com/TailAdmin/free-nextjs-admin-dashboard
