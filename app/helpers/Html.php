<?php

declare(strict_types=1);

namespace Sartu\Helpers;

final class Html
{
    /** Jede Ausgabe in einer Ansicht laeuft hierdurch. */
    public static function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

}
