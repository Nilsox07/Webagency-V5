<?php

declare(strict_types=1);

/**
 * Die 25 Zeichen der Oberflaeche — als ein Inline-Sprite, einmal je Seite.
 *
 * ## Warum es sie gibt
 *
 * `SARTU_CORPORATE_DESIGN.md` §5 verbietet Schmuck, `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`
 * §3.4 verlangt ein Set. Der Widerspruch stand als **Punkt 9** in `OFFENE_ENTSCHEIDUNGEN.md` und
 * ist am **10.08.2026 vom Betreiber entschieden: die Zeichen kommen mit hinein.**
 *
 * Er loest sich so auf, wie §3 desselben Corporate Design ihn selbst andeutet — dort ist Lime
 * verboten fuer „Icons **ohne Funktion**". Die Formulierung setzt voraus, dass es Zeichen mit
 * Funktion gibt. Diese hier haben eine: Sie kennzeichnen Menuepunkte und Zeilenarten.
 *
 * ## Warum ein Sprite und keine Dateien
 *
 * Ein `<use href="#i-...">` je Stelle, eine Definition je Seite. Externe Dateien waeren 25
 * zusaetzliche Abrufe, und die Zeichen sollen die Farbe ihres Elternteils annehmen —
 * `stroke:currentColor` geht nur inline.
 *
 * ## Was hier nicht passiert
 *
 * **Keine Form wird erfunden.** Alle 25 stammen unveraendert aus `design/portalkonzept.html`,
 * dem am 03.08.2026 abgenommenen Entwurf: 24er Raster, 2 Punkt Kontur, offene Enden gerundet.
 * Wer ein sechsundzwanzigstes braucht, zeichnet es dort und uebertraegt es hierher — nicht
 * umgekehrt.
 */

?>
<?php /* **Kein `style`-Attribut.** Bis zum 13.08.2026 stand hier
         `style="position:absolute"` — und die eigene CSP verwirft es: `style-src
         'self'` ohne `unsafe-inline`. Das Sprite blieb damit im Textfluss und nahm
         eine Zeilenhoehe ein: **29 px weisser Rand ueber der Seitenleiste**, auf
         jeder Seite des Kunden- und Adminbereichs. Die Regel steht in
         anwendung.css als `.zeichensatz`. */ ?>
<svg class="zeichensatz" width="0" height="0" aria-hidden="true" focusable="false">
  <symbol id="i-uebersicht" viewBox="0 0 24 24"><rect x="3" y="3" width="7.5" height="9" rx="1.5"/><rect x="13.5" y="3" width="7.5" height="5.5" rx="1.5"/><rect x="13.5" y="12" width="7.5" height="9" rx="1.5"/><rect x="3" y="15.5" width="7.5" height="5.5" rx="1.5"/></symbol>
  <symbol id="i-angebot" viewBox="0 0 24 24"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M9 13h6"/><path d="M9 17h4"/></symbol>
  <symbol id="i-aufgaben" viewBox="0 0 24 24"><path d="m3 6.5 1.8 1.8L8.2 5"/><path d="m3 17.5 1.8 1.8L8.2 16"/><path d="M12 7h9"/><path d="M12 12h9"/><path d="M12 17.5h9"/></symbol>
  <symbol id="i-vorschau" viewBox="0 0 24 24"><rect x="2.5" y="3.5" width="19" height="13.5" rx="2"/><path d="M8.5 21h7"/><path d="M12 17v4"/></symbol>
  <symbol id="i-rechnungen" viewBox="0 0 24 24"><path d="M5 3v18l2.6-1.6L10.2 21l2.6-1.6L15.4 21l2.6-1.6L20 21V3H5z"/><path d="M9 8.5h7"/><path d="M9 12.5h7"/><path d="M9 16.5h4"/></symbol>
  <symbol id="i-domain" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3c2.4 2.6 3.6 5.6 3.6 9s-1.2 6.4-3.6 9c-2.4-2.6-3.6-5.6-3.6-9S9.6 5.6 12 3z"/></symbol>
  <symbol id="i-inhalte" viewBox="0 0 24 24"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7"/><path d="M14 3l4 4v5"/><path d="M20.5 13.5l-5 5-.5 2.5 2.5-.5 5-5a1.4 1.4 0 0 0-2-2z"/></symbol>
  <symbol id="i-vertrag" viewBox="0 0 24 24"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="m9 15.2 1.9 1.9 3.6-3.6"/></symbol>
  <symbol id="i-hilfe" viewBox="0 0 24 24"><path d="M20.5 11.6c0 4.6-3.8 8.3-8.5 8.3-1.4 0-2.7-.3-3.9-.9L3.5 20.5l1.5-4.4a8.1 8.1 0 0 1-1.5-4.5c0-4.6 3.8-8.3 8.5-8.3s8.5 3.7 8.5 8.3z"/></symbol>
  <symbol id="i-anfragen" viewBox="0 0 24 24"><path d="M22 12.5h-5.4l-1.6 2.8H9l-1.6-2.8H2"/><path d="M5.6 4.8 2 12.5V19a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6.5l-3.6-7.7A2 2 0 0 0 16.6 3.6H7.4a2 2 0 0 0-1.8 1.2z"/></symbol>
  <symbol id="i-kunden" viewBox="0 0 24 24"><path d="M6 21V4.5A1.5 1.5 0 0 1 7.5 3h9A1.5 1.5 0 0 1 18 4.5V21"/><path d="M2.5 21h19"/><path d="M9.5 7.5h1.5M13 7.5h1.5M9.5 11.5h1.5M13 11.5h1.5"/><path d="M10 21v-4h4v4"/></symbol>
  <symbol id="i-projekte" viewBox="0 0 24 24"><path d="M3 7a2 2 0 0 1 2-2h3.6l2 2.6H19a2 2 0 0 1 2 2V18a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/></symbol>
  <symbol id="i-audit" viewBox="0 0 24 24"><path d="M3.2 12a8.8 8.8 0 1 0 2.9-6.6L3 8.4"/><path d="M3 3.4v5h5"/><path d="M12 7.6V12l3.4 2"/></symbol>
  <symbol id="i-betreiberdaten" viewBox="0 0 24 24"><path d="M4 6.5h9"/><path d="M17.5 6.5H20"/><circle cx="15.2" cy="6.5" r="2.2"/><path d="M4 17.5h9"/><path d="M17.5 17.5H20"/><circle cx="15.2" cy="17.5" r="2.2"/><path d="M4 12h2.5"/><path d="M11 12h9"/><circle cx="8.8" cy="12" r="2.2"/></symbol>
  <symbol id="i-rechtstexte" viewBox="0 0 24 24"><path d="M12 3.5v17"/><path d="M7.5 20.5h9"/><path d="M4.5 7h15"/><path d="M4.5 7 2 13h5L4.5 7z"/><path d="M19.5 7 17 13h5l-2.5-6z"/></symbol>
  <symbol id="i-testmail" viewBox="0 0 24 24"><rect x="2.5" y="4.5" width="19" height="15" rx="2"/><path d="m3 6.5 9 6.5 9-6.5"/></symbol>
  <symbol id="i-stand" viewBox="0 0 24 24"><circle cx="6" cy="19" r="2.8"/><circle cx="18" cy="5" r="2.8"/><path d="M8.8 19H15a3.5 3.5 0 0 0 0-7H9a3.5 3.5 0 0 1 0-7h6.2"/></symbol>
  <symbol id="i-hinweis" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 11.2v5"/><path d="M12 7.7v.2"/></symbol>
  <symbol id="i-filter" viewBox="0 0 24 24"><path d="M3.5 6h17"/><path d="M7 12h10"/><path d="M10.5 18h3"/></symbol>
  <symbol id="i-bild" viewBox="0 0 24 24"><rect x="3" y="4.5" width="18" height="15" rx="2"/><circle cx="8.5" cy="10" r="1.6"/><path d="m3.6 17.6 4.9-4.9 3.4 3.4 3-3 6.1 6.1"/></symbol>
  <symbol id="i-pfeil" viewBox="0 0 24 24"><path d="M4 12h15"/><path d="m13 5.6 6.4 6.4-6.4 6.4"/></symbol>
  <symbol id="i-export" viewBox="0 0 24 24"><path d="M12 3.5v11.5"/><path d="m7 10.5 5 5 5-5"/><path d="M4.5 20h15"/></symbol>
  <symbol id="i-haken" viewBox="0 0 24 24"><path d="m5 12.6 4.6 4.6L19 7.2"/></symbol>
  <symbol id="i-abmelden" viewBox="0 0 24 24"><path d="M9.5 21H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3.5"/><path d="m15.5 16.5 4.5-4.5-4.5-4.5"/><path d="M20 12H9.5"/></symbol>
  <symbol id="i-fenster" viewBox="0 0 24 24"><path d="M14 4h6v6"/><path d="M20 4 11 13"/><path d="M18 14.5V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4.5"/></symbol>
</svg>
