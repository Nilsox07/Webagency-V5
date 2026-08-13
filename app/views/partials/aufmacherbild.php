<?php

declare(strict_types=1);

use Sartu\Helpers\Html;

/**
 * Das Gerät im Aufmacher — `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4b, entschieden 10.08.2026.
 *
 * ## Was hier steht und was nicht
 *
 * **Der Rahmen ist selbst gezeichnet.** Laptop und angeschnittenes Telefon entstehen aus
 * Flächen, Kanten und einer CSS-Perspektive. Keine gekaufte Vorlage, keine heruntergeladene,
 * **kein externer Abruf** — die Regel gilt auch für ein Bild, das nur hübsch ist.
 *
 * **Auf dem Bildschirm steht eine echte Aufnahme.** `sartu-kundenbereich-muster.webp` zeigt
 * den gebauten Kundenbereich mit Musterdaten. Der abgenommene Entwurf zeichnet den
 * Bildschirminhalt an dieser Stelle mit Balken und Platzhalterzeilen nach;
 * `10_WEBSITE_SARTU.md` §8 verbietet genau das. **Übernommen ist der Rahmen, nicht der
 * nachgebaute Inhalt.**
 *
 * ## Der Tausch gegen das gerenderte Mockup
 *
 * Die Anweisung vom 13.08.2026 will den gezeichneten Rahmen durch
 * `public/assets/bild/geraet-aufmacher.webp` ersetzen. **Die Datei liegt nicht im
 * Repository** und hat nie darin gelegen — nachgesucht über `git rev-list --all --objects`.
 * Dieselbe Anweisung regelt den Fall: „Liegt die Datei nicht im Repo, überspring diesen
 * Block und melde es — erfinde kein Ersatzbild."
 *
 * **Am 13.08.2026 war der Rahmen einmal gelöscht und ist am selben Tag wiederhergestellt
 * worden.** §4b ist Rang 1 und hält das Gerät fest: „Im Aufmacher steht ein Gerät in
 * leichter Schrägstellung — Laptop mit angeschnittenem Telefon davor." Überspringen heißt
 * nicht tun, bis die Datei da ist — nicht: die Entscheidung vom 10.08.2026 zurücknehmen.
 *
 * **Liegt das Mockup, wird es genommen**, und der gezeichnete Rahmen entfällt an dieser
 * Stelle von selbst. `MarkupTest::testDerGeraeterahmenWirdGetauschtSobaldDasMockupVorliegt`
 * hält beide Zustände fest.
 *
 * ## Warum hier kein Bildplatz steht
 *
 * `partials/bildplatz.php` bleibt unverändert und gilt weiter für **Musterprojekte** und das
 * **Gründerfoto**. Dort ist das Bild der Beleg, und ein leerer Rahmen an einer
 * Vertrauensstelle ist unzulässig (§5). Im Aufmacher ist das Bild kein Beleg, sondern das
 * eigene Produkt — und das gibt es.
 *
 * ## Der Vermerk steht neben dem Bild
 *
 * `Musteransicht` ist gebunden (§5 Sektion 1, „Visual rechts mit Kennzeichen"). Bis zum
 * 13.08.2026 lag er **auf** dem Bildschirm und verdeckte dort die Kopfzeile der Aufnahme;
 * der Auftrag verlangt „neben dem Bild, nicht darauf". Er steht deshalb als `figcaption`
 * unter dem Gerät — im selben Blick, ohne etwas zu verdecken.
 *
 * @var string $marke der gebundene Vermerk
 */

$mockup = '/assets/bild/geraet-aufmacher.webp';
$gerendert = is_file(dirname(__DIR__, 3) . '/public' . $mockup);
$aufnahme = '/assets/bild/sartu-kundenbereich-muster.webp';

?>
<figure class="geraet">
<?php if ($gerendert): ?>
  <?php /* Das gerenderte Mockup bringt Rahmen und Aufnahme in einer Datei mit —
           der gezeichnete Rahmen entfaellt hier deshalb vollstaendig. */ ?>
  <div class="geraet__schirm">
    <img src="<?= Html::e($mockup) ?>"
         alt="Der Kundenbereich auf einem Laptop: drei offene Aufgaben und der Projektstand."
         width="1600" height="1040" loading="eager" decoding="async">
  </div>
<?php else: ?>
  <div class="geraet__laptop">
    <div class="geraet__deckel">
      <div class="geraet__schirm">
        <img src="<?= Html::e($aufnahme) ?>"
             alt="Der Kundenbereich mit einer Musteransicht: drei offene Aufgaben und der Projektstand."
             width="1600" height="1040" loading="eager" decoding="async">
      </div>
    </div>
    <div class="geraet__sockel" aria-hidden="true"></div>
  </div>

  <div class="geraet__telefon" aria-hidden="true">
    <div class="geraet__schirm">
      <img src="<?= Html::e($aufnahme) ?>" alt="" width="1600" height="1040">
    </div>
  </div>
<?php endif; ?>

  <figcaption class="geraet__marke"><?= Html::e($marke) ?></figcaption>
</figure>
