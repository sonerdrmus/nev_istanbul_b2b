<?php

namespace App\Support;

/**
 * Hızlı sipariş görsellerini (yeni çoklu `images[]` ve eski tekil `image_path`/`image_url`) tek biçime getirir.
 */
class QuickOrderImages
{
    /**
     * @param  mixed  $quickOrder  `{notes, images:[{path,url}], image_path, image_url}`
     * @return list<array{path: ?string, url: string}>
     */
    public static function list(mixed $quickOrder): array
    {
        if (! is_array($quickOrder)) {
            return [];
        }

        $result = [];
        $seen = [];
        $push = function (?string $path, ?string $url) use (&$result, &$seen): void {
            $url = trim((string) $url);
            if ($url === '') {
                return;
            }
            if (isset($seen[$url])) {
                return;
            }
            $seen[$url] = true;
            $result[] = ['path' => $path !== null && $path !== '' ? $path : null, 'url' => $url];
        };

        if (isset($quickOrder['images']) && is_array($quickOrder['images'])) {
            foreach ($quickOrder['images'] as $image) {
                if (is_array($image)) {
                    $push($image['path'] ?? null, $image['url'] ?? null);
                } elseif (is_string($image)) {
                    $push(null, $image);
                }
            }
        }

        $push($quickOrder['image_path'] ?? null, $quickOrder['image_url'] ?? null);

        return $result;
    }

    /**
     * E-posta gibi mutlak URL gereken yerler için.
     *
     * @return list<array{path: ?string, url: string}>
     */
    public static function absoluteList(mixed $quickOrder): array
    {
        return array_map(function (array $image): array {
            $url = $image['url'];
            if (! preg_match('#^https?://#i', $url)) {
                $url = url($url);
            }

            return ['path' => $image['path'], 'url' => $url];
        }, self::list($quickOrder));
    }
}
