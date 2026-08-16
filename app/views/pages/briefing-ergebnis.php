<?php

declare(strict_types=1);

use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Bedarfsscheck;
use Sartu\Services\Foerdertexte;

/**
 * Das Ergebnis **vor** den Kontaktdaten — Website-Lastenheft §9.3.
 *
 * ## Was am 16.08.2026 neu gebaut wurde, und was nicht
 *
 * **Nicht geändert:** Empfehlungsregel, Reihenfolge, Datenmodell, jeder Satz aus
 * `Empfehlungstext`, der Pflichthinweis und der Förderhinweis. §9.3 verbietet weiterhin
 * Paketwechsel-Knöpfe, Zusatzoptionen und SEO-Auswahl; es gibt genau eine primäre Handlung.
 *
 * **Geändert wurde die Form.** Vorher stand hier eine 46-rem-Spalte aus dem internen Layout
 * mit einer hellgrünen Rundkarte und drei weissen Pillen für `1 Seite`, `1 Korrekturrunde`
 * und `Schutz S`. Drei Befunde dazu:
 *
 * | Befund | Warum es zählt |
 * |---|---|
 * | Lime-Fläche rund 180.000 px² | Die öffentliche Obergrenze ist 40.000. Lime ist Handlung, nicht Hintergrund einer Zusammenfassung |
 * | Fakten als Pillen | Eine Pille ist Handlung, Status, Filter oder Kategorie. `1 Korrekturrunde` ist keins davon — es ist ein Wert zu einer Beschriftung |
 * | schmale Insel | Der Bildschirm, an dem die Kaufentscheidung fällt, sah aus wie ein fremdes Formular |
 *
 * ## Der Aufbau jetzt
 *
 * Zweispaltig ab 900 px, asymmetrisch: **links die Entscheidung** — Vorzeile, Überschrift,
 * Begründung, Folgesatz —, **rechts das Datenblatt**. Das Datenblatt trägt den Preis als
 * Zahl, das erste Jahr darunter und die drei Angaben als beschriftete Zeilen mit Linie
 * dazwischen. Es steht auf `--ink`, weil es die Entscheidung ist; Lime bleibt am Knopf.
 *
 * Unter beidem die Handlungsgruppe: ein Knopf, ein Textlink. `Angaben ändern` war vorher ein
 * zweiter Knopf in `--ruhig` und damit optisch fast gleich stark.
 *
 * @var array<string,mixed>      $text        aus Empfehlungstext::fuer()
 * @var array<string,mixed>|null $preise      die Zeile aus der Preistabelle
 * @var string|null              $preiszeile
 * @var string|null              $erstesJahr
 */

/*
 * Die drei Angaben als **Beschriftung und Wert**, nicht als Aufzählung.
 *
 * `Schutz S` stand vorher allein in einer Pille und erklärte sich nicht. Es ist die
 * fachliche Bezeichnung der Betriebsstufe und bleibt es — sie bekommt nur die Beschriftung
 * dazu, die sie lesbar macht. Kein neuer Leistungsinhalt.
 */
$angaben = [];

if ($preise !== null) {
    $angaben['Umfang'] = (string) $preise['seiten'];

    if ((int) $preise['korrekturrunden'] > 0) {
        $angaben['Korrekturrunden'] = (string) (int) $preise['korrekturrunden'];
    }

    $angaben['Betrieb'] = (string) $preise['schutz'];
}

?>
<div class="bahn ergebnis">
  <div class="ergebnis__entscheidung">
    <p class="vorzeile">Ihr Ergebnis</p>
    <h1><?= Html::e((string) $text['ueberschrift']) ?></h1>

<?php if ($text['satz'] !== null): ?>
    <p class="lede"><?= Html::e((string) $text['satz']) ?></p>
<?php endif; ?>

<?php if ($text['aufzaehlung'] !== []): ?>
    <?php /* **Die Begründung, nicht eine Chipwand.** §9.3 verlangt, die Empfehlung „aus den
             Antworten zu begründen"; das ist ein Satz mit einer Aufzählung darin, keine
             Sammlung runder Marken. Die Angaben sind Text — sie sind weder anklickbar noch
             ein Zustand. */ ?>
    <p class="ergebnis__grund">Das haben Sie angegeben:</p>
    <ul class="ergebnis__gruende">
<?php foreach ($text['aufzaehlung'] as $glied): ?>
      <li><?= Html::e(ucfirst((string) $glied)) ?></li>
<?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if ($text['folge'] !== null): ?>
    <p><?= Html::e((string) $text['folge']) ?></p>
<?php endif; ?>
  </div>

<?php if ($preiszeile !== null && $preise !== null): ?>
  <?php /* **Das Datenblatt.** Dunkel, weil es die Entscheidung trägt — nicht, weil eine
           Messzahl eine dunkle Fläche verlangt. Es sieht ausdrücklich **nicht** aus wie die
           hervorgehobene Stufe der öffentlichen Preistabelle: kein Lime, keine Empfehlungs-
           fahne, keine zweite Auswahl daneben. Hier ist nichts mehr zu wählen. */ ?>
  <aside class="datenblatt" aria-label="Ihre Empfehlung in Zahlen">
    <p class="datenblatt__marke">Empfohlener Umfang</p>
    <p class="datenblatt__paket"><?= Html::e((string) $preise['name']) ?></p>

    <p class="datenblatt__preis"><?= Html::e((string) $preiszeile) ?></p>
    <p class="datenblatt__jahr">Erstes Jahr <?= Html::e((string) $erstesJahr) ?></p>

    <dl class="datenblatt__zeilen">
<?php foreach ($angaben as $was => $wert): ?>
      <div>
        <dt><?= Html::e((string) $was) ?></dt>
        <dd><?= Html::e((string) $wert) ?></dd>
      </div>
<?php endforeach; ?>
    </dl>
  </aside>
<?php endif; ?>

  <div class="ergebnis__handlung">
    <?php /* **Eine primäre Handlung.** `Angaben ändern` war bis zum 16.08.2026 ein zweiter
             Knopf in `--ruhig` — optisch fast gleich stark, obwohl er zurückführt. Als
             Textlink steht er in derselben Gruppe und bleibt sichtbar. */ ?>
    <p class="ergebnis__knoepfe">
      <a class="knopf" href="/briefing/kontakt">Empfehlung unverbindlich prüfen lassen<span class="pfeil" aria-hidden="true">→</span></a>
      <a class="textlink" href="/briefing/<?= Html::e((string) Bedarfsscheck::SCHRITTE) ?>">Angaben ändern</a>
    </p>

    <p class="fussnote"><?= Html::e((string) $text['hinweis']) ?></p>

    <?php /* **Der Förderhinweis — `17_SEITEN_SARTU.md` §2.3, gebaut am 15.08.2026.** Er ist
             keine Werbung, sondern Sorgfalt: Ein Förderantrag muss vor Vorhabensbeginn
             gestellt sein. Wer hier weiterklickt, annimmt und erst danach von der Förderung
             erfährt, verliert den Anspruch — durch unseren eigenen Ablauf.

             §2.3: „Er darf den Pflichthinweis darüber nicht verdrängen — kleinere Stufe,
             eigene Zeile." Als abgesetzte Randnotiz unter der Entscheidung erfüllt er
             beides. */ ?>
    <p class="randnotiz"><?= Html::e(Foerdertexte::HINWEIS_ERGEBNIS) ?>
      <a href="<?= Html::e(Foerdertexte::PFAD) ?>">Was zur Förderung gilt</a>.</p>
  </div>
</div>
