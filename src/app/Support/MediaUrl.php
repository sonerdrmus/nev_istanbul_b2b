<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Public disk medya URL'leri. Tarayıcı/CDN önbelleğini kırmak için sorgu parametresi eklenir.
 *
 * Öncelik: MEDIA_QUERY_VERSION (.env) doluysa onu kullanır; boşsa dosyanın ilk 64KB + boyut + mtime ile parmak izi.
 */
final class MediaUrl
{
    public static function public(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $url = Storage::disk('public')->url($path);

        $full = storage_path('app/public/'.$path);
        $bust = trim((string) config('app.media_query_version', ''));
        if ($bust === '' && is_file($full)) {
            $bust = self::fingerprint($full);
        }
        if ($bust !== '') {
            $sep = str_contains($url, '?') ? '&' : '?';
            $url .= $sep.'v='.rawurlencode($bust);
        }

        return $url;
    }

    private static function fingerprint(string $full): string
    {
        $mtime = (int) (@filemtime($full) ?: 0);
        $size = (int) (@filesize($full) ?: 0);
        $sample = '';
        if ($size > 0 && $h = @fopen($full, 'rb')) {
            $sample = (string) fread($h, (int) min(65536, $size));
            fclose($h);
        }

        return substr(hash('sha256', $sample.'|'.$size.'|'.$mtime), 0, 24);
    }
}
