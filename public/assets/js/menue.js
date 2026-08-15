/*
 * Das mobile Menü — Website-Lastenheft §3.
 *
 * Das Menü bleibt ohne Skript vollständig bedienbar — es ist ein `details`-Element und
 * bleibt eines. Das Skript fügt nur die Falle hinzu, wenn es läuft. Damit gilt
 * Portal-Lastenheft §3 Regel 7 unverändert weiter: Jeder Kernablauf funktioniert mit
 * abgeschaltetem JavaScript.
 *
 * `summary` liefert Öffnen und Schließen. Fokusfalle, Rücksprung aufs Icon, `Esc` und Klick
 * außerhalb liefert es nicht — am 02.08.2026 in Chromium nachgemessen, nachdem zwei
 * Kommentare im Bau das Gegenteil behauptet hatten. Die vier stehen hier, sonst nichts.
 */
(function () {
  'use strict';

  var menue = document.querySelector('details.menue');

  if (!menue) {
    return;
  }

  var knopf = menue.querySelector('summary');

  function zu() {
    if (menue.open) {
      menue.open = false;
    }
  }

  // Unsichtbares faellt raus: Auf breiten Fenstern blendet das CSS das Menue ganz aus.
  function anspringbare() {
    var alle = menue.querySelectorAll('summary, a[href], button:not([disabled])');

    return Array.prototype.filter.call(alle, function (element) {
      return element.offsetParent !== null;
    });
  }

  menue.addEventListener('keydown', function (ereignis) {
    if (!menue.open || ereignis.key !== 'Tab') {
      return;
    }

    var liste = anspringbare();

    if (liste.length === 0) {
      return;
    }

    var erstes = liste[0];
    var letztes = liste[liste.length - 1];

    if (ereignis.shiftKey && document.activeElement === erstes) {
      letztes.focus();
      ereignis.preventDefault();
    } else if (!ereignis.shiftKey && document.activeElement === letztes) {
      erstes.focus();
      ereignis.preventDefault();
    }
  });

  // `Esc` am Dokument: Eine Taste, die nur bei passendem Fokus wirkt, ist schlimmer als
  // keine. Der Klick daneben schliesst ebenfalls — beides verlangt §3.
  document.addEventListener('keydown', function (ereignis) {
    if (ereignis.key === 'Escape') {
      zu();
    }
  });

  /*
   * Der Klick daneben braucht eine echte Flaeche. `!menue.contains(ziel)` allein konnte nie
   * zutreffen: Das offene Blatt liegt mit `inset: 0` ueber dem Fenster und ist ein Kind des
   * `details` — jeder Klick fiel innerhalb (15.08.2026). `.menue__hinterlegung` traegt die
   * Flaeche jetzt; `contains` bleibt fuer breite Fenster.
   */
  document.addEventListener('click', function (ereignis) {
    if (!menue.open) {
      return;
    }

    var ziel = ereignis.target;

    if (ziel instanceof Element && ziel.hasAttribute('data-hinterlegung')) {
      zu();

      return;
    }

    if (!menue.contains(ziel)) {
      zu();
    }
  });

  // Beim Oeffnen bleibt der Fokus, wo der Browser ihn hinsetzt: auf `summary`.
  menue.addEventListener('toggle', function () {
    if (!menue.open && knopf) {
      knopf.focus();
    }
  });
})();
