<?php

declare(strict_types=1);

use Sartu\Ansicht;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Html;

/**
 * `/login` — Portal-Lastenheft §6.2. Die Texte stehen dort gebunden.
 *
 * @var array{telefon:?string,email:?string} $notweg
 * @var string|null $fehler
 * @var string|null $hinweis
 * @var string $wert
 */

?>
<h1>Anmelden</h1>
<p>Geben Sie Ihre E-Mail-Adresse ein. Wir schicken Ihnen einen Anmeldelink — ein Passwort
brauchen Sie nicht.</p>

<?= Ansicht::teil('partials/meldungen', [
    'fehler'   => $fehler === null ? [] : [$fehler],
    'hinweise' => $hinweis === null ? [] : [$hinweis],
]) ?>

<form method="post" action="/login">
  <?= Csrf::feld() ?>
  <?= Ansicht::teil('components/feld', [
      'name'                   => 'email',
      'beschriftung'           => 'E-Mail-Adresse',
      'art'                    => 'email',
      'wert'                   => $wert,
      'pflicht'                => true,
      'autovervollstaendigung' => 'email',
  ]) ?>
  <button type="submit" class="knopf">Anmeldelink senden</button>
</form>

<?= Ansicht::teil('partials/notweg', ['notweg' => $notweg]) ?>
