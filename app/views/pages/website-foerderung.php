<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Format;
use Sartu\Helpers\Html;
use Sartu\Services\Foerderprogramme;
use Sartu\Services\Foerdertexte as T;
use Sartu\Services\Websitetexte;

/**
 * `/foerderung` — `17_SEITEN_SARTU.md` §6, neun Blöcke.
 *
 * **Die Adresse stand seit dem 09.08.2026 in `16_SEO_GEO_SARTU.md` mit Priorität 0.9 und
 * lieferte 404.** Aufgefallen ist das erst am 14.08.2026, als `OberflaecheTest` die
 * Adressliste zum ersten Mal gegen die Spezifikation hielt.
 *
 * **Nicht in der Hauptnavigation** (§6): `10_WEBSITE_SARTU.md` §2 bindet sie auf sechs
 * Punkte. Verlinkt ist die Seite aus `/preise`, dem Ratgeber-Hub und dem Fussbereich.
 *
 * @var array<string,string>|null $auftragslage
 * @var string $preishinweis
 */

?>
<?= Ansicht::teil('partials/seitenaufmacher', [
    'vorzeile'     => T::VORZEILE,
    'h1'           => T::H1,
    'vorspann'     => T::KURZ,
    'auftragslage' => $auftragslage,
    'preishinweis' => $preishinweis,
    'zweitziel'    => '/preise',
    'zweittext'    => 'Preise ansehen',
]) ?>

<?php /* Block 3 — die Reihenfolge. Sie steht **vor** der Länderübersicht: Wer zuerst die
         Tabelle liest, hat womöglich schon beauftragt, wenn er hier ankommt. */ ?>
<section class="abschnitt abschnitt--sand">
  <div class="bahn schmal">
    <h2><?= Html::e(T::REIHENFOLGE_H2) ?></h2>
<?php foreach (T::REIHENFOLGE as $absatz): ?>
    <p><?= Html::e($absatz) ?></p>
<?php endforeach; ?>
  </div>
</section>

<?php /* Block 4 — die Absage. §6, Berichtigung vom 09.08.2026: Der Block „führt mit dieser
         Antwort, nicht mit einer Verheissung". Er steht dunkel, weil er die Aussage der
         Seite trägt und ihr wehtut. */ ?>
<section class="abschnitt abschnitt--dunkel">
  <div class="bahn schmal">
    <h2><?= Html::e(T::WARUM_H2) ?></h2>
<?php foreach (T::WARUM as $absatz): ?>
    <p><?= Html::e($absatz) ?></p>
<?php endforeach; ?>

    <h3><?= Html::e(T::DOCH_H3) ?></h3>
    <ul class="hakenliste">
<?php foreach (T::DOCH as $fall): ?>
      <li><?= Html::e($fall) ?></li>
<?php endforeach; ?>
    </ul>

    <p class="hervor"><?= Html::e(T::DOCH_GRENZE) ?></p>
  </div>
</section>

<?php /* Block 5 — die Übersicht. **Das Prüfdatum steht hier, nicht im Fussbereich** (§6,
         Pflichtangabe): Eine Förderübersicht ohne Datum ist eine Übersicht, deren Alter
         niemand kennt. Keine Summe, keine Quote — Begründung im Absatz darunter. */ ?>
<section class="abschnitt" id="laender">
  <div class="bahn">
    <h2><?= Html::e(T::LAENDER_H2) ?></h2>
    <p class="lede"><?= Html::e(T::LAENDER_EINLEITUNG) ?></p>
    <?php /* **Zwei Daten, nicht eines.** Der Inhalt ist am 09.08.2026 an der Primärquelle
             geprüft worden, die sechzehn Adressen am 15.08.2026 angefordert. Das sind zwei
             verschiedene Aussagen, und die Seite behauptet keine, die sie nicht belegt: Eine
             Adresse, die heute antwortet, sagt nichts darüber, ob das Programm heute noch
             läuft. */ ?>
    <p class="marken">Inhalt an der Quelle geprüft am
      <?= Html::e(Format::datum(Foerderprogramme::GEPRUEFT_AM)) ?>, Verweise zuletzt
      angefordert am <?= Html::e(Format::datum(Foerderprogramme::LINKS_GEPRUEFT_AM)) ?>.</p>

    <div class="tabellenrolle">
      <table class="zahlentabelle">
        <caption class="leise"><?= Html::e(T::LAENDER_OHNE_BETRAEGE) ?></caption>
        <thead>
          <tr>
            <th scope="col">Land</th>
            <th scope="col">Programm</th>
            <th scope="col">Stelle</th>
            <th scope="col">Bedingung</th>
            <th scope="col">Stand</th>
          </tr>
        </thead>
        <tbody>
<?php foreach (Foerderprogramme::alle() as $programm): ?>
          <tr>
            <th scope="row"><?= Html::e($programm['land']) ?></th>
            <td><?= Html::e($programm['programm']) ?></td>
            <?php /* **Der Verweis ist der Kern der Übersicht, nicht ihre Zugabe.**
                     `FOERDERUNG_KONZEPT.md`: verweisen statt kopieren. Vier von sechzehn
                     Programmen standen in Sekundärübersichten als aktiv und sind es nicht —
                     wer keinen Weg zur Quelle anbietet, ist selbst eine Sekundärübersicht.

                     `rel="noopener"` ohne `target`: Der Verweis öffnet im selben Fenster.
                     Ein erzwungenes neues Fenster nimmt dem Leser die Zurück-Taste. */ ?>
            <td><a href="<?= Html::e($programm['quelle']) ?>" rel="noopener nofollow"><?= Html::e($programm['stelle']) ?></a></td>
            <td><?= Html::e($programm['bedingung']) ?></td>
            <?php /* **Der Stand ist eine Beschriftung, kein Satz.** Er kommt aus einem
                     festen Wortschatz von fünf Werten (`Foerderprogramme::STATUS`), und elf
                     der sechzehn Zeilen tragen denselben — das ist bei einer Statusspalte
                     richtig so und wäre in Fliesstext ein Fehler. `.marken` ist die
                     Beschriftungsklasse des Auftritts; sie setzt ihn in Monoschrift und
                     nimmt ihn zugleich aus der Satzanfangsprüfung von `OberflaecheTest`,
                     die laufende Prosa misst. Die Spalte `Bedingung` daneben bleibt darin —
                     sie ist Prosa. */ ?>
            <td><p class="marken"><?= Html::e(Foerderprogramme::status($programm['status'])) ?></p></td>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php /* Block 6 — der ehrliche Gegenpunkt. `FOERDERUNG_KONZEPT.md` §3.5: „Für einen Kunden,
         der schnell will, ist Förderung ein Nachteil. Das zu sagen, passt exakt zur
         Positionierung." */ ?>
<section class="abschnitt abschnitt--sand">
  <div class="bahn schmal">
    <h2><?= Html::e(T::KOSTET_H2) ?></h2>
    <p class="lede"><?= Html::e(T::KOSTET_EINLEITUNG) ?></p>

    <?php /* Beschriftung und Angabe sind ein Paar, kein Aufzählungspunkt — deshalb `dl`,
             mit demselben Bauteil wie auf `/musterprojekte`. Keine neue Form. */ ?>
    <dl class="musterteile musterteile--breit">
<?php foreach (T::KOSTET as $was => $text): ?>
      <div>
        <dt><?= Html::e($was) ?></dt>
        <dd><?= Html::e($text) ?></dd>
      </div>
<?php endforeach; ?>
    </dl>
  </div>
</section>

<?php /* Block 7 — die drei Stufen. `FOERDERUNG_KONZEPT.md` §4: Stufe 1 und 2 sind ohnehin
         Teil eines Angebots, Stufe 3 wäre eine eigene Leistung mit eigenem Risiko. Ein
         Partner wird **nicht** genannt — es gibt keinen benannten. */ ?>
<section class="abschnitt">
  <div class="bahn">
    <h2><?= Html::e(T::SARTU_H2) ?></h2>

    <ul class="leistungszeilen">
<?php foreach (T::stufen() as $stufe): ?>
      <li>
        <h3><?= Html::e($stufe['was']) ?></h3>
        <p><?= Html::e($stufe['text']) ?></p>
        <p class="marken"><?= Html::e($stufe['gilt']) ?></p>
      </li>
<?php endforeach; ?>
    </ul>

    <p class="hervor"><?= Html::e(T::RECHTSHINWEIS) ?></p>
  </div>
</section>

<section class="zusage">
  <div class="bahn">
    <p><?= Html::e(T::ZUSAGE) ?></p>
  </div>
</section>

<section class="abschnitt abschnitt--sand" id="fragen">
  <div class="bahn schmal">
    <h2>Häufige Fragen</h2>
    <?= Ansicht::teil('partials/fragenliste', ['fragen' => T::fragen()]) ?>
  </div>
</section>

<?php /* Block 8 — der Bedarfsscheck. §6 nennt ihn als geteilten Block; er ist auf jeder
         Seite dasselbe Handlungsfeld. */ ?>
<section class="abschluss">
  <div class="bahn">
    <div class="handlungsfeld">
      <div class="handlungsfeld__text">
        <h2><?= Html::e(T::ABSCHLUSS_H2) ?></h2>
      </div>

      <div class="handlungsfeld__handlung">
        <?= Ansicht::teil('partials/handlungsblock', [
            'auftragslage' => $auftragslage,
            'preishinweis' => Websitetexte::ABSCHLUSSHINWEIS . ' ' . $preishinweis,
            'zweitziel'    => '/preise',
            'zweittext'    => 'Preise ansehen',
            'dunkel'       => true,
        ]) ?>
      </div>
    </div>
  </div>
</section>
