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

        $bust = trim((string) config('app.media_query_version', ''));
        if ($bust === '' && config('filesystems.disks.public.driver', 'local') !== 's3') {
            $full = storage_path('app/public/'.$path);
            if (is_file($full)) {
                $bust = self::fingerprint($full);
            }
        }
        if ($bust !== '') {
            $sep = str_contains($url, '?') ? '&' : '?';
            $url .= $sep.'v='.rawurlencode($bust);
        }

        return $url;
    }

    /**
     * PNG/JPEG için yanındaki .webp varsa <picture> üretir; yoksa düz <img>.
     *
     * @param  array<string, string>  $attributes
     */
    public static function pictureHtml(?string $path, string $alt = '', string $imgClass = '', array $attributes = []): string
    {
        if ($path === null || $path === '') {
            return '';
        }

        $fallback = self::public($path);
        $attrParts = [];
        foreach ($attributes as $key => $value) {
            $attrParts[] = e((string) $key).'="'.e((string) $value).'"';
        }
        $attrString = $attrParts !== [] ? ' '.implode(' ', $attrParts) : '';
        $classAttr = $imgClass !== '' ? ' class="'.e($imgClass).'"' : '';
        $img = '<img src="'.e($fallback).'" alt="'.e($alt).'"'.$classAttr.$attrString.'>';

        $relative = ltrim($path, '/');
        if (str_starts_with($relative, 'http://') || str_starts_with($relative, 'https://')) {
            return $img;
        }

        $webpRelative = preg_replace('/\.(jpe?g|png)$/i', '.webp', $relative);
        if (! is_string($webpRelative) || $webpRelative === $relative) {
            return $img;
        }

        if (config('filesystems.disks.public.driver', 'local') === 's3') {
            try {
                if (! Storage::disk('public')->exists($webpRelative)) {
                    return $img;
                }
            } catch (\Throwable) {
                return $img;
            }
        } else {
            $webpFull = storage_path('app/public/'.$webpRelative);
            if (! is_file($webpFull)) {
                return $img;
            }
        }

        $webpUrl = self::public($webpRelative);

        return '<picture><source srcset="'.e($webpUrl).'" type="image/webp">'.$img.'</picture>';
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
