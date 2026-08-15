<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Backfill EN/IT display columns from catalog map + TR source. Never touches matching keys.
 */
final class CatalogLocaleBackfill
{
    /**
     * @param  array<int, array{0: string, 1: string}>  $pairs  list of [sourceColumn, prefix] e.g. [['name', 'name'], ['title', 'title']]
     */
    public static function table(string $table, array $pairs): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        $updated = 0;
        $select = ['id'];
        $writable = [];
        foreach ($pairs as [$source, $prefix]) {
            if (! Schema::hasColumn($table, $source)) {
                continue;
            }
            $enCol = $prefix.'_en';
            $itCol = $prefix.'_it';
            if (! Schema::hasColumn($table, $enCol) || ! Schema::hasColumn($table, $itCol)) {
                continue;
            }
            $select[] = $source;
            $select[] = $enCol;
            $select[] = $itCol;
            $writable[] = [$source, $enCol, $itCol];
        }

        if ($writable === []) {
            return 0;
        }

        DB::table($table)->orderBy('id')->select($select)->chunkById(200, function ($rows) use ($table, $writable, &$updated): void {
            foreach ($rows as $row) {
                $payload = [];
                foreach ($writable as [$source, $enCol, $itCol]) {
                    $sourceVal = $row->{$source} ?? null;
                    $pair = CatalogLabelTranslator::pair($sourceVal);
                    $currentEn = $row->{$enCol} ?? null;
                    $currentIt = $row->{$itCol} ?? null;
                    $sourceTrim = trim((string) $sourceVal);
                    $sameAsSource = static fn (?string $value): bool => trim((string) $value) === ''
                        || strcasecmp(trim((string) $value), $sourceTrim) === 0;

                    $hasExplicitEn = $pair['en'] !== '' && strcasecmp(trim($pair['en']), $sourceTrim) !== 0;
                    $hasExplicitIt = $pair['it'] !== '' && strcasecmp(trim($pair['it']), $sourceTrim) !== 0;

                    $newEn = $hasExplicitEn ? $pair['en'] : ($sameAsSource($currentEn) ? $pair['en'] : (string) $currentEn);
                    $newIt = $hasExplicitIt ? $pair['it'] : ($sameAsSource($currentIt) ? $pair['it'] : (string) $currentIt);

                    if (($currentEn ?? null) !== ($newEn !== '' ? $newEn : null)) {
                        $payload[$enCol] = $newEn !== '' ? $newEn : null;
                    }
                    if (($currentIt ?? null) !== ($newIt !== '' ? $newIt : null)) {
                        $payload[$itCol] = $newIt !== '' ? $newIt : null;
                    }
                }
                if ($payload !== []) {
                    DB::table($table)->where('id', $row->id)->update($payload);
                    $updated++;
                }
            }
        });

        return $updated;
    }

    /**
     * @return array<int, array{0: string, 1: list<array{0: string, 1: string}>}>
     */
    public static function targets(): array
    {
        return [
            ['products', [['name', 'name']]],
            ['categories', [['name', 'name']]],
            ['product_variations', [['name', 'name']]],
            ['product_variation_options', [['option_value', 'option_value']]],
            ['interface_fabric_type_variations', [['name', 'name']]],
            ['interface_color_variations', [['name', 'name']]],
            ['interface_certificate_variations', [['name', 'name']]],
            ['interface_mold_model_variations', [['name', 'name']]],
            ['interface_delivery_method_variations', [['name', 'name']]],
            ['interface_delivery_method_sub_options', [['name', 'name']]],
            ['interface_packaging_preference_variations', [['name', 'name']]],
            ['interface_label_type_variations', [['name', 'name'], ['description_title', 'description_title']]],
            ['interface_packaging_materials', [['name', 'name']]],
            ['interface_packaging_customizations', [['name', 'name']]],
            ['size_tables', [['name', 'name'], ['title', 'title']]],
            ['product_customization_print_techniques', [['name', 'name']]],
            ['product_customization_rows', [['position_name', 'position_name']]],
        ];
    }

    public static function all(): int
    {
        $total = 0;
        foreach (self::targets() as [$table, $pairs]) {
            $total += self::table($table, $pairs);
        }

        return $total;
    }

    /**
     * Translate leftover TR copies via catalog map then machine translation. Does not change matching keys.
     *
     * @param  callable(string, string, string):void|null  $onTranslated  fn($source, $en, $it)
     * @return array{unique: int, updated: int}
     */
    public static function machineTranslateRemaining(?callable $onTranslated = null): array
    {
        [$unique, $neededEn, $neededIt] = self::collectUntranslatedSources();

        /** @var array<string, array{en: string, it: string}> $cache */
        $cache = [];

        foreach ($unique as $source) {
            $pair = self::resolveTranslatedPair($source, in_array($source, $neededEn, true), in_array($source, $neededIt, true));
            $cache[self::cacheKey($source)] = $pair;
            if ($onTranslated) {
                $onTranslated($source, $pair['en'], $pair['it']);
            }
        }

        $updated = 0;
        foreach (self::targets() as [$table, $pairs]) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            $select = ['id'];
            $writable = [];
            foreach ($pairs as [$source, $prefix]) {
                if (! Schema::hasColumn($table, $source)) {
                    continue;
                }
                $enCol = $prefix.'_en';
                $itCol = $prefix.'_it';
                if (! Schema::hasColumn($table, $enCol) || ! Schema::hasColumn($table, $itCol)) {
                    continue;
                }
                $select[] = $source;
                $select[] = $enCol;
                $select[] = $itCol;
                $writable[] = [$source, $enCol, $itCol];
            }
            if ($writable === []) {
                continue;
            }

            DB::table($table)->orderBy('id')->select($select)->chunkById(200, function ($rows) use ($table, $writable, $cache, &$updated): void {
                foreach ($rows as $row) {
                    $payload = [];
                    foreach ($writable as [$source, $enCol, $itCol]) {
                        $sourceVal = trim((string) ($row->{$source} ?? ''));
                        if ($sourceVal === '') {
                            continue;
                        }
                        $pair = $cache[self::cacheKey($sourceVal)] ?? CatalogLabelTranslator::pair($sourceVal);
                        $currentEn = $row->{$enCol} ?? null;
                        $currentIt = $row->{$itCol} ?? null;
                        if (self::isUntranslated($currentEn, $sourceVal) && ($pair['en'] ?? '') !== '') {
                            $payload[$enCol] = $pair['en'];
                        }
                        if (self::isUntranslated($currentIt, $sourceVal) && ($pair['it'] ?? '') !== '') {
                            $payload[$itCol] = $pair['it'];
                        }
                    }
                    if ($payload !== []) {
                        DB::table($table)->where('id', $row->id)->update($payload);
                        $updated++;
                    }
                }
            });
        }

        return ['unique' => count($unique), 'updated' => $updated];
    }

    /**
     * @return array{0: list<string>, 1: list<string>, 2: list<string>}
     */
    private static function collectUntranslatedSources(): array
    {
        $unique = [];
        $neededEn = [];
        $neededIt = [];

        foreach (self::targets() as [$table, $pairs]) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            foreach ($pairs as [$source, $prefix]) {
                $enCol = $prefix.'_en';
                $itCol = $prefix.'_it';
                if (! Schema::hasColumn($table, $source) || ! Schema::hasColumn($table, $enCol) || ! Schema::hasColumn($table, $itCol)) {
                    continue;
                }
                foreach (DB::table($table)->select($source, $enCol, $itCol)->get() as $row) {
                    $src = trim((string) ($row->{$source} ?? ''));
                    if ($src === '') {
                        continue;
                    }
                    $enUn = self::isUntranslated($row->{$enCol} ?? null, $src);
                    $itUn = self::isUntranslated($row->{$itCol} ?? null, $src);
                    if (! $enUn && ! $itUn) {
                        continue;
                    }
                    $unique[$src] = true;
                    if ($enUn) {
                        $neededEn[$src] = true;
                    }
                    if ($itUn) {
                        $neededIt[$src] = true;
                    }
                }
            }
        }

        return [array_keys($unique), array_keys($neededEn), array_keys($neededIt)];
    }

    /**
     * @return array{en: string, it: string}
     */
    private static function resolveTranslatedPair(string $source, bool $needEn, bool $needIt): array
    {
        $mapped = CatalogLabelTranslator::pair($source);
        $en = $mapped['en'];
        $it = $mapped['it'];

        if (self::isStableCode($source)) {
            return ['en' => $source, 'it' => $source];
        }

        if ($needEn && self::isUntranslated($en, $source)) {
            if (self::looksTurkish($source)) {
                $translated = MachineTranslator::translate($source, 'tr', 'en');
                if (filled($translated)) {
                    $en = $translated;
                }
                usleep(180000);
            } else {
                $en = $source;
            }
        }

        if ($needIt && self::isUntranslated($it, $source)) {
            $from = self::looksTurkish($source) ? 'tr' : 'en';
            $base = $from === 'tr' ? $source : (filled($en) && $en !== $source ? $en : $source);
            $translated = MachineTranslator::translate($base, $from, 'it');
            if (filled($translated)) {
                $it = $translated;
            }
            usleep(180000);
        }

        return [
            'en' => $en !== '' ? $en : $source,
            'it' => $it !== '' ? $it : $source,
        ];
    }

    private static function isUntranslated(?string $value, string $source): bool
    {
        $value = trim((string) $value);

        return $value === '' || strcasecmp($value, trim($source)) === 0;
    }

    private static function looksTurkish(string $text): bool
    {
        if (preg_match('/[çğıöşüÇĞİÖŞÜ]/u', $text)) {
            return true;
        }

        return (bool) preg_match(
            '/\b(ve|ile|için|ürün|kumaş|beden|renk|seçiniz|seçimi|seçimiz|yaka|tişört|tisort|çanta|şapka|erkek|kadın|kadin|bayan|çocuk|cocuk|kalıp|kalip|etiket|ambalaj|amabalaj|teslim|teslimat|baskı|baski|göğüs|gogus|ön|arka|sol|sağ|koyu|açık|acik|gri|krema|vanilya|kum|kahve|bronz|bebek|pembesi|tarçın|tarcin|zeytin|orman|nane|mavisi|lacivert|gece|petrol|çelik|celik|doğal|dogal|pastel|orta|canlı|canli|fuşya|fusya|lavanta|indigo|parlak|altın|altin|haki|zümrüt|zumrut|gümüş|gumus|kül|kul|kömür|komur|deniz|kırık|kirik|beyaz|siyah|kırmızı|kirmizi|mavi|yeşil|yesil|sarı|sari|pembe|mor|turuncu|promosyon|tekstili|torba|kilitli|dikim|yeri|mat|ense|biye|biyesi|tercihi|giyim|gömlek|gomlek|kazak|ceket|bluz|yelek|pantolon|etek|kaban|mont|elbise|atkı|atki|kravat|kraliyet|espresso|turkuaz|deneme|istemiyorum|ribana|fermuar|astar|dikiş|dikişi|kuşgözü|yırtmaç|kanguru|kordon|kapama|mıknatıs|iplik|kalitesi|varyasyon|bel|cebi|cebi|kapüşon|kapuson)\b/iu',
            $text
        );
    }

    private static function isStableCode(string $text): bool
    {
        $text = trim($text);
        if ($text === '') {
            return true;
        }
        if (preg_match('/^(XS|S|M|L|XL|XXL|XXXL|3XL|4XL|5XL|SLIM|SLİM|FIT|REGULAR|OVERSIZE|EXW|FOB|CIF|DAP|DDP)$/iu', $text)) {
            return true;
        }
        if (preg_match('/^(GOST|OCS|GRS|RCS|GOTS|BCI)$/i', $text)) {
            return true;
        }
        if (preg_match('/\bTCX\b/i', $text)) {
            return true;
        }
        if (preg_match('/^(OEKO\s*TEX)/i', $text)) {
            return true;
        }
        if (preg_match('/\b(EXW|FOB|CIF|DAP|DDP)\b/i', $text) && preg_match('/^[A-Z0-9\s–\-]+$/u', $text)) {
            return true;
        }
        if (preg_match('/^(BETTER COTTON|EKTEOKS)/i', $text)) {
            return true;
        }
        if (preg_match('/^soner[-_]/i', $text)) {
            return true;
        }
        if (preg_match('/^\d{1,4}$/', $text)) {
            return true;
        }

        return false;
    }

    private static function cacheKey(string $source): string
    {
        $source = str_replace(["\u{200B}", "\u{200C}", "\u{200D}", "\u{00A0}", "\u{FEFF}"], '', trim($source));

        return mb_strtolower(preg_replace('/\s+/u', ' ', $source) ?? $source, 'UTF-8');
    }
}
