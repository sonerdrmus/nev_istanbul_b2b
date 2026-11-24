<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

final class StoreLocale
{
    /** @return list<string> */
    public static function supported(): array
    {
        $locales = config('store.locales', ['tr', 'en', 'it']);

        return array_values(array_filter(array_map('strval', is_array($locales) ? $locales : ['tr', 'en', 'it'])));
    }

    public static function default(): string
    {
        $default = (string) config('store.default_locale', config('app.locale', 'tr'));

        return self::isSupported($default) ? $default : (self::supported()[0] ?? 'tr');
    }

    public static function isSupported(?string $locale): bool
    {
        return $locale !== null && in_array($locale, self::supported(), true);
    }

    public static function current(): string
    {
        $locale = app()->getLocale();

        return self::isSupported($locale) ? $locale : self::default();
    }

    public static function apply(?string $locale): string
    {
        $resolved = self::isSupported($locale) ? (string) $locale : self::default();
        app()->setLocale($resolved);
        session(['locale' => $resolved]);
        URL::defaults(['locale' => $resolved]);

        return $resolved;
    }

    /**
     * Mevcut storefront URL'ini başka bir dil önekine çevirir.
     */
    public static function alternateUrl(string $targetLocale, ?Request $request = null): string
    {
        $request ??= request();
        $targetLocale = self::isSupported($targetLocale) ? $targetLocale : self::default();
        $path = '/'.ltrim($request->getPathInfo() ?: '/', '/');
        $supported = self::supported();
        $pattern = '#^/('.implode('|', array_map('preg_quote', $supported)).')(/|$)#';

        if (preg_match($pattern, $path, $m)) {
            $rest = substr($path, strlen($m[1]) + 1);
            $newPath = '/'.$targetLocale.($rest === '' || $rest === '/' ? '' : $rest);
        } else {
            $newPath = '/'.$targetLocale.($path === '/' ? '' : $path);
        }

        $query = $request->getQueryString();

        return url($newPath).($query ? '?'.$query : '');
    }

    /** @return array<string, string> locale => absolute URL */
    public static function alternateUrls(?Request $request = null): array
    {
        $urls = [];
        foreach (self::supported() as $locale) {
            $urls[$locale] = self::alternateUrl($locale, $request);
        }

        return $urls;
    }
}
