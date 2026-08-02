<?php

declare(strict_types=1);

use Sartu\Helpers\Csrf;
use Sartu\Helpers\Html;

/**
 * Der Einstiegsbildschirm des Bedarfsschecks — Website-Lastenheft §9.1.
 *
 * **Eine Änderung am vorgegebenen Wortlaut, begründet:** §9.1 nennt die H1 „Welche Website
 * passt **wirklich** zu Ihrem Unternehmen?". `SARTU_TEXTREGELN.md` Regel 7 Liste A streicht
 * „wirklich" als Verstärkung. Die Textregeln stehen in der Rangfolge auf Rang 3, das
 * Website-Lastenheft auf Rang 5 — also gewinnt Rang 3. Die gekürzte Fassung steht
 * zusätzlich als Muster im Texter-Skill.
 *
 * @var string|null $kontaktweg
 */

$vertrauenspunkte = [
    'Dauert etwa 3 Minuten',
    'Preis vor Kontaktdaten',
    'Kein Pflichttermin',
    'Keine Auswahl von Zusatzoptionen',
    'Unverbindlich bis zum geprüften Angebot',
];

?>
<p class="vorzeile">Handwerk · Praxen · Kanzleien · Ladengeschäfte</p>
<h1>Welche Website passt zu Ihrem Unternehmen?</h1>

<p class="lead">Sie müssen weder Paket noch Seitenzahl, Designrichtung oder SEO-Stufe kennen.
Beantworten Sie wenige Fragen zu Ihrem Geschäft — danach sehen Sie eine vorläufige Empfehlung
mit Preis.</p>

<ul class="punkte">
<?php foreach ($vertrauenspunkte as $punkt): ?>
  <li><?= Html::e($punkt) ?></li>
<?php endforeach; ?>
</ul>

<form method="post" action="/briefing/start">
  <?= Csrf::feld() ?>
  <button type="submit" class="knopf">Bedarf prüfen lassen</button>
</form>

<p class="fussnote">Thema 1 von 5 beginnt auf der nächsten Seite.</p>

<?php if ($kontaktweg !== null): ?>
<p class="fussnote">Sprechen können Sie trotzdem mit uns. Schreiben Sie an
  <a href="mailto:<?= Html::e($kontaktweg) ?>"><?= Html::e($kontaktweg) ?></a>.</p>
<?php endif; ?>
