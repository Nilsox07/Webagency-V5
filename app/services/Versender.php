<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Wer eine Mail hinausschickt.
 *
 * ## Warum es diese Schnittstelle gibt
 *
 * `Mailversand` ist `final` und spricht SMTP. Solange kein Test prüfte, **ob** eine Mail
 * rausging, reichte das: Die Dienste riefen den Versand auf, und lokal fing Mailpit ihn ab.
 *
 * Portal-Lastenheft §4b.6 ändert das. Beim Kontaktformular entsteht **kein Datensatz** — die
 * Mail ist der einzige Träger der Rückfrage. Ob sie rausgeht, was drinsteht und was
 * passiert, wenn sie **nicht** rausgeht, ist damit prüfbares Verhalten.
 *
 * ## Warum nicht einfach `final` streichen
 *
 * Weil `final` etwas aussagt: Es gibt genau einen Weg, eine Mail zu verschicken, und der
 * geht über SMTP. Das bleibt so. Was dazukommt, ist eine **Naht** — ein Test darf einen
 * anderen Versender einsetzen, ohne dass die Produktion einen zweiten bekommt.
 *
 * `Mailversand` ist weiterhin `final` und weiterhin die einzige Umsetzung im Anwendungscode.
 */
interface Versender
{
    /**
     * @throws MailversandFehler wenn die Mail nicht hinausgeht
     */
    public function senden(string $an, string $betreff, string $klartext, ?string $html = null): void;
}
