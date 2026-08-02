<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Helpers\Env;

final class SmtpZugang
{
    public function __construct(
        public readonly string $host,
        public readonly int $port,
        public readonly string $benutzer,
        public readonly string $passwort,
        public readonly string $absender,
        public readonly string $absenderName,
    ) {
    }

    public static function ausKonfiguration(): self
    {
        return new self(
            Env::require('SMTP_HOST'),
            (int) (Env::get('SMTP_PORT', '25') ?? '25'),
            Env::get('SMTP_USER', '') ?? '',
            Env::get('SMTP_PASS', '') ?? '',
            Env::require('MAIL_FROM'),
            Env::get('MAIL_FROM_NAME', 'SARTU') ?? 'SARTU',
        );
    }
}
