<?php

declare(strict_types=1);

namespace Sartu;

/**
 * Eine Antwort als Wert, nicht als Nebenwirkung.
 *
 * Damit laesst sich der Dispatcher im Test aufrufen, ohne einen Webserver zu starten und
 * ohne gegen die Arbeitsdatenbank zu laufen. Die Testfaelle 41, 43, 47, 49 und 73 pruefen
 * genau das, was der Browser bekommt.
 */
final class Antwort
{
    /** @param array<string,string> $kopfzeilen */
    private function __construct(
        public readonly int $status,
        public readonly array $kopfzeilen,
        public readonly string $rumpf,
    ) {
    }

    /** @param array<string,string> $kopfzeilen */
    public static function html(string $rumpf, int $status = 200, array $kopfzeilen = []): self
    {
        return new self($status, ['Content-Type' => 'text/html; charset=utf-8'] + $kopfzeilen, $rumpf);
    }

    public static function text(string $rumpf, int $status = 200): self
    {
        return new self($status, ['Content-Type' => 'text/plain; charset=utf-8'], $rumpf);
    }

    public static function weiter(string $ziel, int $status = 302): self
    {
        return new self($status, ['Location' => $ziel], '');
    }

    /** @param array<string,string> $kopfzeilen */
    public function mitKopfzeilen(array $kopfzeilen): self
    {
        return new self($this->status, $kopfzeilen + $this->kopfzeilen, $this->rumpf);
    }

    public function senden(): void
    {
        http_response_code($this->status);
        foreach ($this->kopfzeilen as $name => $wert) {
            header($name . ': ' . $wert, true);
        }
        echo $this->rumpf;
    }
}
