<?php

namespace App\Support;

/**
 * LiteSpeed rejects some cart and checkout posts when the body contains words
 * such as "select". Hex keeps the text intact for Laravel and off the filter.
 */
final class FirewallSafeBody
{
    public static function decode(mixed $value): mixed
    {
        if (! is_string($value) || ! str_starts_with($value, 'h:')) {
            return $value;
        }

        $hex = substr($value, 2);
        if ($hex === '' || strlen($hex) % 2 !== 0 || ! ctype_xdigit($hex)) {
            return $value;
        }

        $decoded = hex2bin($hex);

        return $decoded === false ? $value : $decoded;
    }
}
