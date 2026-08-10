<?php

declare(strict_types=1);

use Sartu\Helpers\Csrf;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;

/**
 * `/admin/belege` — `18_BELEGE_UND_ZAHLUNG.md` Abschnitte 6 und 8.
 *
 * Zwei Dinge auf einer Seite, weil sie zusammengehören: Was an den Steuerberater geht, und
 * was auf dem Weg dorthin liegengeblieben ist.
 *
 * @var string $von
 * @var string $bis
 * @var list<array<string,mixed>> $abweichungen
 * @var list<string> $fehler
 * @var list<string> $hinweise
 */

?>

<?php foreach ($fehler as $meldung): ?>
<p class="hinweis hinweis--wichtig"><?= Html::e($meldung) ?></p>
<?php endforeach; ?>
<?php foreach ($hinweise as $meldung): ?>
<p class="hinweis"><?= Html::e($meldung) ?></p>
<?php endforeach; ?>

<div class="karte">
  <h2>Übergabe an den Steuerberater</h2>
  <p>Eine Tabelle mit jeder Rechnung und jeder Stornorechnung des Zeitraums — Belegdatum,
  Nummer, Kunde, Netto, Steuersatz, Steuerbetrag, Brutto, Zahlungsdatum und der Verweis auf
  die Belegdatei. Maßgeblich ist das Ausstellungsdatum, nicht der Tag der Anlage.</p>
  <p class="leise">Die Übergabe bucht nicht und trifft keine steuerliche Aussage. Welches
  Format Ihr Steuerberater möchte, klären Sie mit ihm — diese Tabelle nimmt jeder.</p>

  <form method="post" action="/admin/belege/export">
    <?= Csrf::feld() ?>
    <div class="feld">
      <label for="feld-von">Zeitraum ab</label>
      <input type="date" id="feld-von" name="von" value="<?= Html::e($von) ?>" required>
    </div>
    <div class="feld">
      <label for="feld-bis">Zeitraum bis</label>
      <input type="date" id="feld-bis" name="bis" value="<?= Html::e($bis) ?>" required>
    </div>
    <button type="submit" class="knopf">Tabelle herunterladen</button>
  </form>
</div>

<div class="karte">
  <h2>Zahlungsvorgänge ohne Wirkung</h2>
<?php if ($abweichungen === []): ?>
  <p>Hier steht jede Zahlungsmeldung, die zu keiner Rechnung passte oder deren Betrag oder
  Währung abwich. Zurzeit liegt keine vor.</p>
<?php else: ?>
  <p>Diese Meldungen haben <strong>nichts</strong> verändert. Prüfen Sie den Eingang beim
  Zahlungsdienst und tragen Sie ihn gegebenenfalls von Hand ein.</p>
  <ul class="pruefliste">
<?php foreach ($abweichungen as $zeile): ?>
    <li>
      <span><?= Html::e(Format::datumZeit((string) $zeile['received_at'])) ?></span>
      <span>
        <?= Html::e((string) $zeile['provider_event_id']) ?>
<?php if (is_string($zeile['number'] ?? null)): ?>
        · Rechnung <?= Html::e((string) $zeile['number']) ?>
<?php endif; ?>
        · <?= Html::e((string) $zeile['result'] === 'unbekannt' ? 'keine Rechnung dazu' : 'Betrag oder Währung weicht ab') ?>
      </span>
    </li>
<?php endforeach; ?>
  </ul>
<?php endif; ?>
</div>
