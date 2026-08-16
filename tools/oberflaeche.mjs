/**
 * Misst die ausgelieferte Oberflaeche und gibt das Ergebnis als JSON aus.
 *
 * ## Warum ein eigenes Werkzeug und nicht PHPUnit allein
 *
 * `tests/OberflaecheTest.php` prueft neun Eigenschaften. Vier davon — Ueberlauf, Hoehe,
 * Sprungabstand und Lime-Flaeche — sind **berechnete Layoutwerte**. Sie stehen nirgends im
 * Markup; sie entstehen erst, wenn ein Browser das CSS anwendet. Ein Test, der sie aus dem
 * HTML herleiten wollte, wuerde die CSS-Kaskade nachbauen und dabei genau die Fehler
 * uebersehen, die er finden soll.
 *
 * Deshalb: Dieses Werkzeug misst im echten Browser, der Test bewertet die Zahlen. Die
 * Trennung haelt die Grenzwerte dort, wo sie hingehoeren — in `OberflaecheTest.php`, mit
 * Begruendung je Zeile.
 *
 * ## Aufruf
 *
 *     node tools/oberflaeche.mjs http://localhost:8080 /pfad1 /pfad2 …
 *
 * Ohne Playwright oder ohne Browser bricht es mit Rueckgabewert 2 ab und schreibt den Grund
 * nach stderr. Der Test unterscheidet das von einem Messfehler.
 */

const BREITEN = [1920, 1440, 1024, 768, 390, 320];
const BROWSER = process.env.SARTU_CHROMIUM || '/opt/pw-browsers/chromium-1194/chrome-linux/chrome';

/**
 * Playwright ueber `createRequire`, nicht ueber `import`.
 *
 * `NODE_PATH` wirkt nur auf die CommonJS-Aufloesung; ein `await import('playwright')` sucht
 * ausschliesslich neben dieser Datei und in `node_modules` des Projekts. Da Playwright hier
 * global installiert ist, fand der ESM-Weg es nicht — und meldete „Playwright fehlt", obwohl
 * es da war.
 */
import { createRequire } from 'node:module';

let chromium;
try {
  ({ chromium } = createRequire(import.meta.url)('playwright'));
} catch (fehler) {
  process.stderr.write(
    'Playwright fehlt. Global installieren oder NODE_PATH auf das Verzeichnis setzen, '
    + 'in dem es liegt (im Container: /opt/node22/lib/node_modules).\n'
  );
  process.exit(2);
}

const [basis, ...pfade] = process.argv.slice(2);

if (!basis || pfade.length === 0) {
  process.stderr.write('Aufruf: node tools/oberflaeche.mjs <basis-url> <pfad> [<pfad> …]\n');
  process.exit(2);
}

let browser;
try {
  browser = await chromium.launch({ executablePath: BROWSER, args: ['--no-sandbox'] });
} catch (fehler) {
  process.stderr.write(`Kein Browser unter ${BROWSER}: ${fehler.message}\n`);
  process.exit(2);
}

/** Im Browser ausgewertet — alles, was nur dort zu sehen ist. */
function messung(fensterbreite) {
  const doc = document.documentElement;

  // ---- Ueberlaufkandidaten: was ragt hinaus, ohne dass der Elternteil es tut?
  const taeter = [];
  for (const el of document.querySelectorAll('body *')) {
    const r = el.getBoundingClientRect();
    if (r.width <= 0 || r.height <= 0) continue;
    if (r.right + window.scrollX <= fensterbreite + 0.5) continue;
    const e = el.parentElement;
    if (e && e.getBoundingClientRect().right + window.scrollX > fensterbreite + 0.5) continue;
    taeter.push({
      wer: el.tagName.toLowerCase() + '.' + (el.className.baseVal ?? el.className ?? '').toString().trim().split(/\s+/)[0],
      ueber: Math.round(r.right + window.scrollX - fensterbreite),
      geclippt: (() => {
        let a = el.parentElement;
        while (a) {
          const o = getComputedStyle(a).overflowX;
          if (o === 'hidden' || o === 'clip' || o === 'auto' || o === 'scroll') return true;
          a = a.parentElement;
        }
        return false;
      })(),
    });
  }

  // ---- Sprungziele: Abstand zur klebenden Kopfzeile
  const kopf = document.querySelector('.seitenkopf');
  const kopfhoehe = kopf ? Math.round(kopf.getBoundingClientRect().height) : 0;
  const sprungziele = [];
  for (const el of document.querySelectorAll('main [id]')) {
    sprungziele.push({
      id: el.id,
      abstand: Math.round(parseFloat(getComputedStyle(el).scrollMarginTop) || 0),
    });
  }

  // ---- Bildplaetze: traegt jeder ein Seitenverhaeltnis?
  const bildplaetze = [];
  for (const el of document.querySelectorAll('.bildplatz')) {
    const r = el.getBoundingClientRect();
    bildplaetze.push({
      verhaeltnis: getComputedStyle(el).aspectRatio,
      erklaert: el.getAttribute('data-verhaeltnis'),
      breite: Math.round(r.width),
      hoehe: Math.round(r.height),
    });
  }

  // ---- Lime-Flaechen: Rang 1, Farbsystem Fassung 3 — Lime ist Flaeche fuer Knoepfe,
  //      Badges, Textmarker und kleine Bloecke, kein vollflaechiges Band.
  const lime = getComputedStyle(doc).getPropertyValue('--lime').trim().toLowerCase();
  const alsRgb = (() => {
    const probe = document.createElement('span');
    probe.style.color = lime;
    document.body.appendChild(probe);
    const wert = getComputedStyle(probe).color;
    probe.remove();
    return wert;
  })();

  const limeflaechen = [];
  for (const el of document.querySelectorAll('body *')) {
    const cs = getComputedStyle(el);
    if (cs.backgroundColor !== alsRgb) continue;
    const r = el.getBoundingClientRect();
    if (r.width <= 0 || r.height <= 0) continue;
    limeflaechen.push({
      wer: el.tagName.toLowerCase() + '.' + (el.className.baseVal ?? el.className ?? '').toString().trim().split(/\s+/)[0],
      flaeche: Math.round(r.width * r.height),
    });
  }

  // ---- Dunkle Flaechen: der Wechsel hell-dunkel-hell ist der Rhythmus der Seite.
  //      Zwei Gattungen, absichtlich getrennt gezaehlt:
  //      `bahnen`  — randlos dunkel ueber die volle Breite (`.abschnitt--dunkel`, `.zusage`)
  //      `felder`  — dunkle Flaeche **in** einem hellen Abschnitt (`.handlungsfeld`)
  const ink = (() => {
    const probe = document.createElement('span');
    probe.style.color = getComputedStyle(doc).getPropertyValue('--ink').trim();
    document.body.appendChild(probe);
    const wert = getComputedStyle(probe).color;
    probe.remove();
    return wert;
  })();

  const dunkelbahnen = [];
  const dunkelfelder = [];
  for (const el of document.querySelectorAll('main *')) {
    if (getComputedStyle(el).backgroundColor !== ink) continue;
    const r = el.getBoundingClientRect();
    if (r.height < 120) continue;
    const eintrag = {
      wer: el.tagName.toLowerCase() + '.' + (el.className.baseVal ?? el.className ?? '').toString().trim().split(/\s+/)[0],
      hoehe: Math.round(r.height),
    };
    if (r.width >= fensterbreite * 0.95) dunkelbahnen.push(eintrag);
    else dunkelfelder.push(eintrag);
  }

  // ---- Baender: die animierten Diagonalen des Aufmachers.
  const baender = document.querySelectorAll('.band').length;

  /*
   * ---- Fuellgrad: welchen Anteil einer Flaeche Text und Bild wirklich einnehmen.
   *
   * **Nicht die Summe der Kaesten, sondern ihre Vereinigung.** Verschachtelte Elemente
   * ueberlappen sich; eine Summe zaehlt denselben Absatz mehrfach und meldet Werte ueber
   * 100 %. Gerastert wird deshalb in Zellen von 8 px — jede Zelle zaehlt einmal, egal
   * wie viele Kaesten sie beruehren.
   *
   * Gemessen wird an den **Textzeilen** (`Range.getClientRects()`), nicht an ihren
   * Elternkaesten: Ein Absatz, der eine halbe Zeile fuellt, belegt eine halbe Zeile.
   * Dazu Bilder, SVG und Bildplaetze.
   */
  const RASTER = 8;

  const belegt = (wurzel) => {
    const kaesten = [];
    const lauf = document.createTreeWalker(wurzel, NodeFilter.SHOW_TEXT);
    let knoten;
    while ((knoten = lauf.nextNode())) {
      if (!knoten.nodeValue.trim()) continue;
      const bereich = document.createRange();
      bereich.selectNodeContents(knoten);
      for (const r of bereich.getClientRects()) {
        if (r.width > 0 && r.height > 0) kaesten.push(r);
      }
    }
    for (const el of wurzel.querySelectorAll('img, svg, .bildplatz, .aufnahme')) {
      const r = el.getBoundingClientRect();
      if (r.width > 0 && r.height > 0) kaesten.push(r);
    }

    const zellen = new Set();
    for (const r of kaesten) {
      for (let y = Math.floor(r.top / RASTER); y <= Math.floor((r.bottom - 0.01) / RASTER); y++) {
        for (let x = Math.floor(r.left / RASTER); x <= Math.floor((r.right - 0.01) / RASTER); x++) {
          zellen.add(x + ':' + y);
        }
      }
    }
    return zellen.size * RASTER * RASTER;
  };

  const abschnitte = [];
  for (const el of document.querySelectorAll('main > *')) {
    const r = el.getBoundingClientRect();
    if (r.width <= 0 || r.height <= 0) continue;
    abschnitte.push({
      wer: (el.id || el.className.toString().trim().split(/\s+/)[0] || el.tagName.toLowerCase()),
      hoehe: Math.round(r.height),
      fuellgrad: Math.round((belegt(el) / (r.width * r.height)) * 100),
    });
  }

  const haupt = document.querySelector('main');

  // ---- Wortzahl des sichtbaren Haupttextes, ohne Kopf und Fuss.
  const woerter = haupt
    ? (haupt.innerText.trim().match(/[\p{L}\p{N}][\p{L}\p{N}\-’'.,%€]*/gu) ?? []).length
    : 0;

  return {
    hoehe: doc.scrollHeight,
    scrollWidth: doc.scrollWidth,
    ueberlauf: doc.scrollWidth - fensterbreite,
    kopfhoehe,
    taeter: taeter.slice(0, 6),
    sprungziele,
    bildplaetze,
    limeflaechen: limeflaechen.sort((a, b) => b.flaeche - a.flaeche).slice(0, 6),
    dunkelbahnen,
    dunkelfelder,
    baender,
    woerter,
    fuellgrad: haupt
      ? Math.round((belegt(haupt) / (haupt.getBoundingClientRect().width
        * haupt.getBoundingClientRect().height)) * 100)
      : 0,
    abschnitte,
  };
}

/**
 * **Eine Seite je Breite, nicht je Kombination.**
 *
 * Der erste Bau oeffnete `Pfade x Breiten` Seiten — bei 24 Adressen und sechs Breiten sind
 * das 144 Browserkontexte, und der Lauf brach nach zehn Minuten nicht ab, sondern lief noch.
 * Sechs Kontexte, die nacheinander alle Adressen anfahren, messen dasselbe in einem Bruchteil.
 *
 * **Die Breiten laufen nacheinander, nicht parallel.** Der Entwicklungsserver `php -S` ist
 * einfaedig: Sechs Kontexte, die gleichzeitig eine Seite samt Stilvorlage und Bildern holen,
 * warten gegenseitig auf ihn und kommen nicht zurueck. Gemessen am 14.08.2026 — der Lauf
 * haengt, er bricht nicht ab. Sechs Kontexte nacheinander messen dasselbe in rund einer
 * Minute.
 */
/**
 * ## Der Bedarfsscheck laesst sich nicht anfahren, er muss durchlaufen werden
 *
 * `/briefing/ergebnis`, `/briefing/kontakt` und `/briefing/danke` antworten auf einen direkten
 * Aufruf mit `303` auf den Einstieg. Sie standen deshalb in `OberflaecheTest::OHNE_LAYOUT` und
 * **wurden nie gemessen** — ausgerechnet die drei Bildschirme, die eine Anfrage entstehen
 * lassen. Der Ergebnisbildschirm trug dadurch monatelang eine Lime-Flaeche von rund
 * 180.000 Quadratpixeln, ohne dass die Abnahmepruefung etwas davon wusste.
 *
 * Diese Funktion fuellt die Strecke aus und laesst die Sitzung in dem Zustand zurueck, in dem
 * die drei Seiten wirklich ausgeliefert werden.
 *
 * **Sie fuellt generisch, nicht nach Drehbuch:** erste Auswahl je Frage, Pflichtfelder mit
 * plausiblem Text, Haken gesetzt. Ein Drehbuch mit festen Feldnamen waere bei der naechsten
 * Frage veraltet und wuerde still danebengreifen.
 */
async function funnelDurchlaufen(seite, basis) {
  await seite.goto(basis + '/briefing', { waitUntil: 'load', timeout: 20000 });
  await abschicken(seite);

  // Fuenf Themen. Die Schranke steht bei acht: Wer eine sechste Seite einhaengt, soll eine
  // Messung bekommen und keine Endlosschleife.
  for (let i = 0; i < 8; i++) {
    if (!/^\/briefing\/\d+$/.test(new URL(seite.url()).pathname)) break;
    await fuellen(seite);
    await abschicken(seite);
  }
}

/** Erste Auswahl je Frage, Pflichtfelder gefuellt, Haken gesetzt. */
async function fuellen(seite) {
  await seite.evaluate(() => {
    const gesehen = new Set();

    for (const el of document.querySelectorAll('input[type=radio]')) {
      if (gesehen.has(el.name)) continue;
      gesehen.add(el.name);
      el.checked = true;
    }

    /*
     * **Nur der erste Haken je Gruppe.** Alle zu setzen sah harmlos aus und lief in eine
     * Sperre: Schritt 3 fuehrt „Nichts davon" als Auswahl, die sich mit keiner anderen
     * verbindet — der Server wies ab, die Adresse blieb stehen, und der Lauf drehte sich,
     * bis die Zeitgrenze griff. Gemessen am 16.08.2026.
     */
    const gruppen = new Set();

    for (const el of document.querySelectorAll('input[type=checkbox]')) {
      // Der Honigtopf bleibt leer — hier soll nichts abgewiesen werden.
      if (el.name.startsWith('hp_')) continue;

      const gruppe = el.closest('fieldset') ?? el.closest('.frage') ?? el.form;
      if (gruppen.has(gruppe)) continue;
      gruppen.add(gruppe);
      el.checked = true;
    }

    for (const el of document.querySelectorAll('select')) {
      const wahl = [...el.options].find((o) => o.value !== '');
      if (wahl) el.value = wahl.value;
    }

    const texte = {
      first_name: 'Erika', last_name: 'Mustermann', company: 'Mustermann Sanitär GmbH',
      email: 'anfrage@example.org', phone: '0351 1234567',
    };

    for (const el of document.querySelectorAll('input[type=text], input[type=email], input[type=tel], textarea')) {
      if (el.name.startsWith('hp_') || el.value !== '') continue;
      el.value = texte[el.name] ?? 'Angabe für die Messung';
    }
  });
}

/** Absenden und auf die naechste Seite warten. */
async function abschicken(seite) {
  const vorher = seite.url();

  await seite.click('form button[type=submit]');

  try {
    await seite.waitForURL((u) => u.toString() !== vorher, { timeout: 15000 });
  } catch (fehler) {
    // Bleibt die Adresse gleich, hat der Server das Formular zurueckgewiesen. Der Aufrufer
    // merkt das daran, dass der Pfad kein Schritt mehr ist — und bricht ab, statt zu haengen.
  }

  await seite.waitForLoadState('load');
}

/**
 * Was vor einer Adresse geschehen muss, damit sie ueberhaupt ausgeliefert wird.
 *
 * `/briefing/danke` verlangt zusaetzlich eine abgeschickte Anfrage. Damit die Messung dabei
 * **keinen** Datensatz anlegt, wird der Honigtopf gefuellt: §4b.2 fuehrt einen so erkannten
 * Versuch stillschweigend auf die Danke-Seite, ohne zu speichern. Genau der Zustand, der
 * gemessen werden soll — und kein Eintrag in `leads`.
 */
const VORLAUF = {
  '/briefing/ergebnis': strecke,
  '/briefing/kontakt': strecke,
  '/briefing/danke': async (seite, basis, zustand) => {
    await strecke(seite, basis, zustand);
    await seite.goto(basis + '/briefing/kontakt', { waitUntil: 'load', timeout: 20000 });
    await fuellen(seite);
    await seite.evaluate(() => {
      const topf = document.querySelector('input[name^=hp_]');
      if (topf) topf.value = 'gefüllt für die Messung — §4b.2 verwirft still';
    });
    await abschicken(seite);

    // Das Absenden verwirft den Zwischenstand (§9.5b). Wer danach wieder auf `ergebnis`
    // will, muss die Strecke erneut laufen.
    zustand.komplett = false;
  },
};

/**
 * Die Strecke **einmal je Browserkontext**, nicht einmal je Adresse.
 *
 * `ergebnis` und `kontakt` sind reine GET-Aufrufe und lassen den Zwischenstand stehen. Wer
 * die Strecke fuer beide getrennt durchlaeuft, verdreifacht die Messdauer ohne Gewinn —
 * gemessen: der Lauf ueber vier Adressen lief in fuenf Minuten nicht durch.
 */
async function strecke(seite, basis, zustand) {
  if (zustand.komplett) return;

  await funnelDurchlaufen(seite, basis);
  zustand.komplett = true;
}

const ergebnis = {};
for (const pfad of pfade) ergebnis[pfad] = { breiten: {}, status: 0 };

for (const breite of BREITEN) {
  await (async () => {
  const seite = await browser.newPage({ viewport: { width: breite, height: 900 }, reducedMotion: 'reduce' });
  const zustand = { komplett: false };

  for (const pfad of pfade) {
    if (VORLAUF[pfad]) await VORLAUF[pfad](seite, basis, zustand);

    const antwort = await seite.goto(basis + pfad, { waitUntil: 'load', timeout: 20000 });

    /*
     * Durchrollen, damit `loading="lazy"` die Bilder wirklich holt — sonst misst die
     * Hoehe Rahmen statt Inhalt.
     *
     * **Das Warten braucht eine Zeitgrenze.** Ein Bild mit `loading="lazy"`, das nie in
     * die Naehe des Fensters kommt, bleibt dauerhaft `complete: false` und feuert
     * **weder `onload` noch `onerror`**. Ein `Promise.all` darauf kehrt nie zurueck; der
     * Lauf haengt, statt abzubrechen. Gemessen am 14.08.2026 — die Suche danach hat
     * dreimal zehn Minuten gekostet.
     */
    await seite.evaluate(async () => {
      for (let y = 0; y < document.body.scrollHeight; y += 800) window.scrollTo(0, y);
      window.scrollTo(0, 0);

      const offen = [...document.images].filter((i) => !i.complete);

      await Promise.race([
        Promise.all(offen.map((i) => new Promise((r) => { i.onload = i.onerror = r; }))),
        new Promise((r) => setTimeout(r, 2500)),
      ]);
    });

    ergebnis[pfad].status = antwort ? antwort.status() : 0;
    ergebnis[pfad].breiten[breite] = await seite.evaluate(messung, breite);
  }

    await seite.close();
  })();
}

await browser.close();
process.stdout.write(JSON.stringify(ergebnis, null, 1));
