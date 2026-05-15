<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Renk varyasyonları paneli için düz renk swatch PNG üretir.
 */
final class InterfaceColorSwatchGenerator
{
    private const int SIZE = 80;

    public static function writePng(string $hex, string $absolutePath): void
    {
        [$r, $g, $b] = self::parseHex($hex);

        $image = imagecreatetruecolor(self::SIZE, self::SIZE);
        if ($image === false) {
            throw new InvalidArgumentException('Swatch görseli oluşturulamadı.');
        }

        $fill = imagecolorallocate($image, $r, $g, $b);
        imagefilledrectangle($image, 0, 0, self::SIZE - 1, self::SIZE - 1, $fill);

        $dir = dirname($absolutePath);
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            imagedestroy($image);
            throw new InvalidArgumentException("Dizin oluşturulamadı: {$dir}");
        }

        if (! imagepng($image, $absolutePath)) {
            imagedestroy($image);
            throw new InvalidArgumentException("PNG yazılamadı: {$absolutePath}");
        }

        imagedestroy($image);
    }

    public static function relativePathForHex(string $hex): string
    {
        $normalized = self::normalizeHex($hex);

        return 'interface_color_variations/hex-'.strtolower(ltrim($normalized, '#')).'.png';
    }

    public static function normalizeHex(string $hex): string
    {
        $hex = strtoupper(trim($hex));
        if ($hex === '') {
            throw new InvalidArgumentException('Boş renk kodu.');
        }
        if (! str_starts_with($hex, '#')) {
            $hex = '#'.$hex;
        }
        if (! preg_match('/^#[0-9A-F]{6}$/', $hex)) {
            throw new InvalidArgumentException("Geçersiz hex: {$hex}");
        }

        return $hex;
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private static function parseHex(string $hex): array
    {
        $normalized = self::normalizeHex($hex);
        $digits = substr($normalized, 1);

        return [
            hexdec(substr($digits, 0, 2)),
            hexdec(substr($digits, 2, 2)),
            hexdec(substr($digits, 4, 2)),
        ];
    }
}
