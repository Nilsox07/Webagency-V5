<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Helpers\Env;

/**
 * AES-256-GCM ueber sodium_* — Portal-Lastenheft §1.2.
 *
 * Verschluesselt werden Felder wie `users.totp_secret_enc`. Der Schluessel steht
 * ausschliesslich in ENC_KEY und wird in Setup-Schritt 3 erzeugt, also VOR Schritt 7, in
 * dem das erste TOTP-Geheimnis abgelegt wird. Die alte Fassung des Lastenhefts hatte diese
 * Reihenfolge vertauscht — verschluesseln ohne Schluessel.
 *
 * Wer ENC_KEY verliert, verliert alle TOTP-Geheimnisse. Es gibt keinen zweiten Ablageort
 * und keine Wiederherstellung; das ist der Zweck der Sache.
 */
final class Verschluesselung
{
    private const KENNUNG = 'gcm1';

    public function __construct(private readonly ?string $schluessel = null)
    {
    }

    public function verschluesseln(string $klartext): string
    {
        $this->pruefen();

        $nonce = random_bytes(SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES);
        $geheim = sodium_crypto_aead_aes256gcm_encrypt($klartext, self::KENNUNG, $nonce, $this->schluessel());

        return self::KENNUNG . $nonce . $geheim;
    }

    public function entschluesseln(string $gespeichert): string
    {
        $this->pruefen();

        $kennungLaenge = strlen(self::KENNUNG);
        $nonceLaenge = SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES;

        if (strlen($gespeichert) <= $kennungLaenge + $nonceLaenge
            || substr($gespeichert, 0, $kennungLaenge) !== self::KENNUNG) {
            throw new \RuntimeException('Der verschluesselte Wert hat ein unbekanntes Format.');
        }

        $nonce = substr($gespeichert, $kennungLaenge, $nonceLaenge);
        $geheim = substr($gespeichert, $kennungLaenge + $nonceLaenge);

        $klartext = sodium_crypto_aead_aes256gcm_decrypt($geheim, self::KENNUNG, $nonce, $this->schluessel());

        if ($klartext === false) {
            throw new \RuntimeException('Der Wert liess sich nicht entschluesseln. Passt ENC_KEY noch?');
        }

        return $klartext;
    }

    /** 32 Byte, base64 — so, wie .env.example es beschreibt. */
    public static function schluesselErzeugen(): string
    {
        return base64_encode(random_bytes(32));
    }

    private function pruefen(): void
    {
        if (!sodium_crypto_aead_aes256gcm_is_available()) {
            throw new \RuntimeException(
                'AES-256-GCM ist auf diesem Prozessor nicht verfuegbar. Portal-Lastenheft §1.2 verlangt es. '
                . 'Ein anderes Verfahren wird hier nicht eingesetzt, ohne dass die Vorgabe geaendert wurde.'
            );
        }
    }

    private function schluessel(): string
    {
        $roh = $this->schluessel ?? Env::require('ENC_KEY');
        $binaer = base64_decode($roh, true);

        if ($binaer === false || strlen($binaer) !== SODIUM_CRYPTO_AEAD_AES256GCM_KEYBYTES) {
            throw new \RuntimeException('ENC_KEY ist kein base64-kodierter Schluessel mit 32 Byte.');
        }

        return $binaer;
    }
}
