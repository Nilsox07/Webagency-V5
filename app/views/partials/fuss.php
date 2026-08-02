<?php

declare(strict_types=1);

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;

/**
 * Der Fussbereich zieht Firmenname und Kontakt aus `operator_settings` — nie aus dem
 * Quelltext (§1.4a: eine Quelle fuer alles). Steht dort noch nichts, bleibt die Zeile leer,
 * statt einen Platzhalter zu zeigen.
 */

$betreiber = null;

try {
    $betreiber = (new BetreiberdatenSpeicher())->lesen();
} catch (Throwable) {
    $betreiber = null;
}

?>
<footer class="fussband">
  <div class="bahn">
<?php if (is_array($betreiber)): ?>
    <p><?= Html::e(Format::text(is_string($betreiber['firmenname'] ?? null) ? $betreiber['firmenname'] : null)) ?></p>
<?php endif; ?>
    <p><a href="/impressum">Impressum</a> · <a href="/datenschutz">Datenschutz</a> · <a href="/agb">AGB</a></p>
  </div>
</footer>
