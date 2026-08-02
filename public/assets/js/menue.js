/*
 * Die Fokusfalle im mobilen Menü — Website-Lastenheft §3.
 *
 * Das Menü bleibt ohne Skript vollständig bedienbar — es ist ein `details`-Element und
 * bleibt eines. Das Skript fügt nur die Falle hinzu, wenn es läuft. Damit gilt
 * Portal-Lastenheft §3 Regel 7 unverändert weiter: Jeder Kernablauf funktioniert mit
 * abgeschaltetem JavaScript.
 *
 * §3 verlangt zwei Dinge, die ein `details` nicht kann: „Fokus wird im Overlay gehalten,
 * beim Schließen zurück auf das Menü-Icon." Die stehen hier, sonst nichts.
 */
(function () {
  'use strict';

  var menue = document.querySelector('details.menue');

  if (!menue) {
    return;
  }

  var knopf = menue.querySelector('summary');

  // `offsetParent === null` faellt raus: Auf breiten Fenstern blendet das CSS das ganze
  // Menue aus, und eine Falle um Unsichtbares waere eine Falle um nichts.
  function anspringbare() {
    var alle = menue.querySelectorAll('summary, a[href], button:not([disabled])');

    return Array.prototype.filter.call(alle, function (element) {
      return element.offsetParent !== null;
    });
  }

  menue.addEventListener('keydown', function (ereignis) {
    if (ereignis.key !== 'Tab' || !menue.open) {
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

  // Beim Oeffnen bleibt der Fokus, wo der Browser ihn hinsetzt: auf `summary`.
  menue.addEventListener('toggle', function () {
    if (!menue.open && knopf) {
      knopf.focus();
    }
  });
})();
