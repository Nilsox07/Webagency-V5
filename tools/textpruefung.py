"""Zaehlt die Werte aus SARTU_TEXTREGELN.md Abschnitt 2.

Aufruf:  python3 tools/textpruefung.py DATEI.md

Grenzen des Skripts, beim Lesen der Ausgabe beachten:
  - Ueberschriften und Doppelpunkt-Zeilen werden mit dem Folgesatz verklebt.
    Ein gemeldeter Satz von 24 Woertern kann daher zwei Saetze sein. Nachsehen.
  - "Gegensatzformel" findet auch zitierte Beispiele. Der Mensch entscheidet,
    welcher Treffer eine echte Verwendung ist (Regel 4).
  - Die erste Fassung warf jede Zeile weg, die mit * beginnt, und damit alle
    fettgedruckten Absatzanfaenge. Sie meldete 4 statt 12 Treffern.
    Wer hier etwas aendert, prueft es an einer von Hand gezaehlten Datei.
"""
import re, sys
t = open(sys.argv[1]).read()
prose, incode = [], False
for l in t.split('\n'):
    s = l.strip()
    if s.startswith('```'): incode = not incode; continue
    if incode: continue
    if s.startswith(('|','#')): continue
    if re.match(r'^[-*+]\s', s) or re.match(r'^\d+\.\s', s): continue   # nur echte Listen
    prose.append(re.sub(r'^>\s?','',l))
p = ' '.join(prose)
p = re.sub(r'`[^`]*`','X',p); p = re.sub(r'\*\*|\*','',p); p = re.sub(r'—',' ',p)
s_ = [x.strip() for x in re.split(r'(?<=[.!?])\s+',p) if len(x.strip())>3]
lang = [(len(x.split()),x) for x in s_ if len(x.split())>20]
print("Sätze gesamt            ", len(s_))
print("Längster Satz           ", max(len(x.split()) for x in s_), "Wörter")
print("Sätze über 20 Wörter    ", len(lang))
for n,x in lang: print("   ", n, "|", x[:100])
geg = re.findall(r', (?:nicht|kein|keine|keinen|sondern) |statt dass| statt ', p)
print("Gegensatzformel         ", len(geg))
print("Aufzählungen >3 Glieder ", len(re.findall(r'([A-Za-zÄÖÜäöüß-]+, ){3,}[A-Za-zÄÖÜäöüß-]+', p)))
print("Wortliste (Füllwörter)  ", len(re.findall(r'\b(durchdacht\w*|ganzheitlich\w*|maßgeschneidert\w*|innovativ\w*|hochwertig\w*|effizient\w*|nachhaltig\w*|optimal\w*|zukunftssicher\w*|Mehrwert\w*|Synerg\w*)\b', p)))
print("Behauptung über Dritte  ", len(re.findall(r'(der|ihr|ihre) (kunde|kunden|interessent)[a-zäöüß]* (will|wollen|erwartet|erwarten|sucht|suchen|ruft|rufen|klickt|klicken)|die meisten (kunden|betriebe|handwerker)', p, re.I)))
