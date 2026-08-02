<?php

declare(strict_types=1);

use Sartu\Ansicht;

/**
 * Die Bestaetigungsseite — Portal-Lastenheft §6.2, Wortlaut gebunden.
 *
 * **Sie ist fuer jede Adresse identisch** (Testfall 10). Ob es zu der eingegebenen Adresse
 * einen Zugang gibt, steht hier nicht — weder im Text noch als zusaetzliche Zeile.
 *
 * @var array{telefon:?string,email:?string} $notweg
 */

?>
<h1>Prüfen Sie Ihr Postfach</h1>
<p>Wenn ein Zugang zu dieser Adresse besteht, ist der Anmeldelink unterwegs. Er gilt
15 Minuten und lässt sich einmal verwenden.</p>
<p>Nichts angekommen? Sehen Sie im Spam-Ordner nach oder fordern Sie den Link erneut an.</p>

<p class="knopfreihe"><a class="knopf knopf--ruhig" href="/login">Erneut anfordern</a></p>

<?= Ansicht::teil('partials/notweg', ['notweg' => $notweg]) ?>
