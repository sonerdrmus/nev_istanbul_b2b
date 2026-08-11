<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Lightweight machine translation (TR → EN) via free MyMemory endpoint.
 * Used for product titles/descriptions when English fields are empty.
 */
class MachineTranslator
{
    public static function translate(?string $text, string $from = 'tr', string $to = 'en'): ?string
    {
        $text = trim((string) $text);
        if ($text === '') {
            return null;
        }

        // Already mostly ASCII/English-looking short brand codes: leave as-is
        if ($from === 'tr' && $to === 'en' && ! preg_match('/[çğıöşüÇĞİÖŞÜ]/u', $text) && ! preg_match('/\b(ve|ile|için|ürün|kumaş|beden)\b/iu', $text)) {
            // Still try for multi-word Turkish without specials (e.g. "Erkek Polo")
            if (preg_match('/\b(Erkek|Kadin|Kadın|Cocuk|Çocuk|Bayan|Beden|Tablosu|Seçiniz|Renk)\b/u', $text) === 0 && str_word_count($text) <= 2) {
                // keep processing
            }
        }

        if (Str::length($text) > 4500) {
            return self::translateLong($text, $from, $to);
        }

        return self::request($text, $from, $to);
    }

    public static function translateHtml(?string $html, string $from = 'tr', string $to = 'en'): ?string
    {
        $html = trim((string) $html);
        if ($html === '') {
            return null;
        }

        // Keep simple HTML structure: translate plain text chunks between tags
        if (! str_contains($html, '<')) {
            return self::translate($html, $from, $to);
        }

        $parts = preg_split('/(<[^>]+>)/u', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        if ($parts === false) {
            return self::translate(strip_tags($html), $from, $to);
        }

        $out = '';
        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }
            if (str_starts_with($part, '<')) {
                $out .= $part;
                continue;
            }
            $plain = html_entity_decode($part, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (trim($plain) === '') {
                $out .= $part;
                continue;
            }
            $translated = self::translate($plain, $from, $to);
            $out .= $translated !== null ? e($translated) : $part;
        }

        return $out;
    }

    private static function translateLong(string $text, string $from, string $to): ?string
    {
        $chunks = str_split($text, 4000);
        $translated = [];
        foreach ($chunks as $chunk) {
            $piece = self::request($chunk, $from, $to);
            if ($piece === null) {
                return null;
            }
            $translated[] = $piece;
        }

        return implode('', $translated);
    }

    private static function request(string $text, string $from, string $to): ?string
    {
        try {
            $response = Http::timeout(12)
                ->acceptJson()
                ->get('https://api.mymemory.translated.net/get', [
                    'q' => $text,
                    'langpair' => $from.'|'.$to,
                ]);

            if (! $response->successful()) {
                Log::warning('MachineTranslator HTTP failure', [
                    'status' => $response->status(),
                ]);

                return null;
            }

            $translated = data_get($response->json(), 'responseData.translatedText');
            if (! is_string($translated) || trim($translated) === '') {
                return null;
            }

            // MyMemory sometimes returns QUERY LENGTH LIMIT messages
            if (str_contains(strtoupper($translated), 'MYMEMORY WARNING') || str_contains(strtoupper($translated), 'QUERY LENGTH LIMIT')) {
                return null;
            }

            return html_entity_decode(trim($translated), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        } catch (\Throwable $e) {
            Log::warning('MachineTranslator exception: '.$e->getMessage());

            return null;
        }
    }
}
