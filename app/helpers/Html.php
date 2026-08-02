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

    /** @param array<string,string|null> $attributes */
    public static function attributes(array $attributes): string
    {
        $parts = [];
        foreach ($attributes as $name => $value) {
            if ($value === null) {
                continue;
            }
            $parts[] = self::e($name) . '="' . self::e($value) . '"';
        }

        return implode(' ', $parts);
    }
}
