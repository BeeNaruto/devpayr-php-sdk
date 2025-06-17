<?php

namespace DevPayr\Support;

/**
 * Class Str
 *
 * Provides static string helpers similar to Laravel's Str facade.
 */
class Str
{
    /**
     * Check if a string starts with a given substring or array of substrings.
     */
    public static function startsWith(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && str_starts_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a string ends with a given substring or array of substrings.
     */
    public static function endsWith(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && str_ends_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a string contains a given substring or array of substrings.
     */
    public static function contains(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Limit the number of characters in a string.
     */
    public static function limit(string $value, int $limit = 100, string $end = '...'): string
    {
        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $limit)) . $end;
    }

    /**
     * Convert string to slug (basic implementation).
     */
    public static function slug(string $value, string $separator = '-'): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/i', $separator, $value);
        return trim($value, $separator);
    }

    /**
     * Convert to snake_case.
     */
    public static function snake(string $value): string
    {
        $value = preg_replace('/\s+/u', '', ucwords($value));
        return strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1_', $value));
    }

    /**
     * Convert to camelCase.
     */
    public static function camel(string $value): string
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        return lcfirst(str_replace(' ', '', $value));
    }
}
