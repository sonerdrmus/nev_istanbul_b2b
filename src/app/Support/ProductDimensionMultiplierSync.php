<?php

namespace App\Support;

use App\Models\ColorDimensionMultiplier;
use App\Models\ProductCustomizationSetting;
use App\Models\QuantityDimensionMultiplier;
use App\Models\SizeDimensionMultiplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Ebat / renk / adet çarpanlarını ürün (veya varsayılan şablon: product_id null) kapsamında yükler ve kaydeder.
 */
final class ProductDimensionMultiplierSync
{
    /**
     * Ürünün kendi çarpanı yoksa şablona (product_id null) düşer.
     *
     * @return array<string, array<string, list<array<string, mixed>>>>
     */
    public static function loadGroupedForForm(?int $productId, bool $fallbackToTemplate = true): array
    {
        $scopeProductId = $productId;
        if ($productId !== null && $fallbackToTemplate && ! static::productHasAnyMultiplier($productId)) {
            $scopeProductId = null;
        }

        $loaded = [];
        foreach (PrintTechniqueMultiplierTabs::slugs() as $slug) {
            $loaded[$slug] = [
                'size_rows' => static::loadSizeRows($slug, $scopeProductId),
                'quantity_rows' => static::loadQuantityRows($slug, $scopeProductId),
            ];
            if (PrintTechniqueDimensionMultiplierTypes::supportsColorMultiplier($slug)) {
                $loaded[$slug]['color_rows'] = static::loadColorRows($slug, $scopeProductId);
            }
        }

        // Şablondan doldurulduysa id'leri temizle ki kayıtta ürüne yeni satır olarak yazılsın.
        if ($productId !== null && $scopeProductId === null) {
            return static::stripIds($loaded);
        }

        return $loaded;
    }

    /**
     * @param  array<string, array<string, list<array<string, mixed>>>>  $data
     */
    public static function persistGrouped(?int $productId, array $data): void
    {
        foreach (PrintTechniqueMultiplierTabs::slugs() as $slug) {
            $printData = $data[$slug] ?? [];
            static::persistSizeRows($printData['size_rows'] ?? [], $slug, $productId);
            if (PrintTechniqueDimensionMultiplierTypes::supportsColorMultiplier($slug)) {
                static::persistColorRows($printData['color_rows'] ?? [], $slug, $productId);
            }
            static::persistQuantityRows($printData['quantity_rows'] ?? [], $slug, $productId);
        }
    }

    public static function productHasAnyMultiplier(int $productId): bool
    {
        foreach ([
            'size_dimension_multipliers' => SizeDimensionMultiplier::class,
            'color_dimension_multipliers' => ColorDimensionMultiplier::class,
            'quantity_dimension_multipliers' => QuantityDimensionMultiplier::class,
        ] as $table => $model) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'product_id')) {
                continue;
            }
            if ($model::query()->where('product_id', $productId)->exists()) {
                return true;
            }
        }

        return false;
    }

    public static function applyProductScope(Builder $query, string $table, ?int $productId): Builder
    {
        if (! Schema::hasColumn($table, 'product_id')) {
            return $query;
        }

        if ($productId === null) {
            return $query->whereNull('product_id');
        }

        $modelClass = $query->getModel()::class;
        $hasOwn = $modelClass::query()->where('product_id', $productId)->exists();

        return $hasOwn
            ? $query->where('product_id', $productId)
            : $query->whereNull('product_id');
    }

    /** @return list<array<string, mixed>> */
    public static function loadSizeRows(string $printTechniqueSlug, ?int $productId): array
    {
        if (! static::supportsPrintTechniqueColumn('size_dimension_multipliers')
            && $printTechniqueSlug !== PrintTechniqueDimensionMultiplierTypes::SLUG_EMPRIME) {
            return [];
        }

        $query = SizeDimensionMultiplier::query();
        static::scopeExactProduct($query, 'size_dimension_multipliers', $productId);
        $query->when(
            static::supportsPrintTechniqueColumn('size_dimension_multipliers'),
            fn ($q) => $q->where('print_technique_slug', $printTechniqueSlug),
        );

        return $query->orderBy('sort_order')->orderBy('id')->get()
            ->map(function (SizeDimensionMultiplier $row): array {
                $data = SizeDimensionMultiplier::repeaterRowFromModel($row);
                $data['auto_multiplier'] = number_format((float) ($data['auto_multiplier'] ?? 0), 2, '.', '');

                return $data;
            })
            ->values()
            ->all();
    }

    /** @return list<array<string, mixed>> */
    public static function loadColorRows(string $printTechniqueSlug, ?int $productId): array
    {
        if (! PrintTechniqueDimensionMultiplierTypes::supportsColorMultiplier($printTechniqueSlug)) {
            return [];
        }

        $query = ColorDimensionMultiplier::query();
        static::scopeExactProduct($query, 'color_dimension_multipliers', $productId);
        $query->when(
            static::supportsPrintTechniqueColumn('color_dimension_multipliers'),
            fn ($q) => $q->where('print_technique_slug', $printTechniqueSlug),
        );

        return $query->orderBy('sort_order')->orderBy('color_count')->get()
            ->map(fn (ColorDimensionMultiplier $row): array => ColorDimensionMultiplier::repeaterRowFromModel($row))
            ->values()
            ->all();
    }

    /** @return list<array<string, mixed>> */
    public static function loadQuantityRows(string $printTechniqueSlug, ?int $productId): array
    {
        if (! static::supportsPrintTechniqueColumn('quantity_dimension_multipliers')
            && $printTechniqueSlug !== PrintTechniqueDimensionMultiplierTypes::SLUG_EMPRIME) {
            return [];
        }

        $query = QuantityDimensionMultiplier::query();
        static::scopeExactProduct($query, 'quantity_dimension_multipliers', $productId);
        $query->when(
            static::supportsPrintTechniqueColumn('quantity_dimension_multipliers'),
            fn ($q) => $q->where('print_technique_slug', $printTechniqueSlug),
        );

        return $query->orderBy('sort_order')->orderBy('quantity_from')->get()
            ->map(fn (QuantityDimensionMultiplier $row): array => QuantityDimensionMultiplier::repeaterRowFromModel($row))
            ->values()
            ->all();
    }

    /** @param  list<array<string, mixed>>  $rows */
    public static function persistSizeRows(array $rows, string $printTechniqueSlug, ?int $productId): void
    {
        $keptIds = [];
        $sort = 0;

        foreach ($rows as $row) {
            $label = trim((string) ($row['size_label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $attrs = [
                'product_id' => $productId,
                'print_technique_slug' => $printTechniqueSlug,
                'size_label' => $label,
                'width' => filled($row['width'] ?? null) ? $row['width'] : null,
                'height' => filled($row['height'] ?? null) ? $row['height'] : null,
                'auto_multiplier' => static::normalizeDecimal($row['auto_multiplier'] ?? 1, 1, 2),
                'fixed_multiplier' => static::normalizeFixedMultiplier($row['fixed_multiplier'] ?? null),
                'extra_multiplier' => static::normalizeDecimal($row['extra_multiplier'] ?? 0, 0),
                'sort_order' => $sort * 10,
                'is_active' => (bool) ($row['is_active'] ?? true),
            ];

            if (! Schema::hasColumn('size_dimension_multipliers', 'product_id')) {
                unset($attrs['product_id']);
            }

            $keptIds[] = static::upsertRow(SizeDimensionMultiplier::class, $row, $attrs, $productId);
            $sort++;
        }

        static::deleteRemovedRows(SizeDimensionMultiplier::class, $keptIds, $printTechniqueSlug, $productId);
    }

    /** @param  list<array<string, mixed>>  $rows */
    public static function persistColorRows(array $rows, string $printTechniqueSlug, ?int $productId): void
    {
        $keptIds = [];
        $sort = 0;
        $maxColors = max(1, min(20, (int) ProductCustomizationSetting::instance()->max_color_count));

        foreach ($rows as $row) {
            $colorCount = (int) ($row['color_count'] ?? 0);
            if ($colorCount < 1 || $colorCount > $maxColors) {
                continue;
            }

            $attrs = [
                'product_id' => $productId,
                'print_technique_slug' => $printTechniqueSlug,
                'color_count' => $colorCount,
                'multiplier_price' => static::normalizeDecimal($row['multiplier_price'] ?? 0, 0),
                'sort_order' => $sort * 10,
                'is_active' => (bool) ($row['is_active'] ?? true),
            ];

            if (! Schema::hasColumn('color_dimension_multipliers', 'product_id')) {
                unset($attrs['product_id']);
            }

            $keptIds[] = static::upsertRow(ColorDimensionMultiplier::class, $row, $attrs, $productId);
            $sort++;
        }

        static::deleteRemovedRows(ColorDimensionMultiplier::class, $keptIds, $printTechniqueSlug, $productId);
    }

    /** @param  list<array<string, mixed>>  $rows */
    public static function persistQuantityRows(array $rows, string $printTechniqueSlug, ?int $productId): void
    {
        $keptIds = [];
        $sort = 0;

        foreach ($rows as $row) {
            $from = (int) ($row['quantity_from'] ?? 0);
            $to = (int) ($row['quantity_to'] ?? 0);
            if ($from < 1 || $from > 1000 || $to < 1 || $to > 1000 || $to < $from) {
                continue;
            }

            $attrs = [
                'product_id' => $productId,
                'print_technique_slug' => $printTechniqueSlug,
                'quantity_from' => $from,
                'quantity_to' => $to,
                'multiplier_price' => static::normalizeDecimal($row['multiplier_price'] ?? 0, 0),
                'sort_order' => $sort * 10,
                'is_active' => (bool) ($row['is_active'] ?? true),
            ];

            if (! Schema::hasColumn('quantity_dimension_multipliers', 'product_id')) {
                unset($attrs['product_id']);
            }

            $keptIds[] = static::upsertRow(QuantityDimensionMultiplier::class, $row, $attrs, $productId);
            $sort++;
        }

        static::deleteRemovedRows(QuantityDimensionMultiplier::class, $keptIds, $printTechniqueSlug, $productId);
    }

    private static function scopeExactProduct(Builder $query, string $table, ?int $productId): void
    {
        if (! Schema::hasColumn($table, 'product_id')) {
            return;
        }

        if ($productId === null) {
            $query->whereNull('product_id');
        } else {
            $query->where('product_id', $productId);
        }
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $row
     * @param  array<string, mixed>  $attrs
     */
    private static function upsertRow(string $modelClass, array $row, array $attrs, ?int $productId): int
    {
        if (! empty($row['id'])) {
            $model = $modelClass::query()->find((int) $row['id']);
            if ($model) {
                // Şablondan gelen id ile ürün kaydı çakışmasın: başka ürüne/şablona aitse yeni oluştur.
                $modelProductId = Schema::hasColumn($model->getTable(), 'product_id')
                    ? ($model->product_id !== null ? (int) $model->product_id : null)
                    : null;
                if ($modelProductId === $productId) {
                    $model->update($attrs);

                    return (int) $model->id;
                }
            }
        }

        $model = $modelClass::query()->create($attrs);

        return (int) $model->id;
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  list<int>  $keptIds
     */
    private static function deleteRemovedRows(string $modelClass, array $keptIds, string $printTechniqueSlug, ?int $productId): void
    {
        $table = (new $modelClass)->getTable();
        $query = $modelClass::query();
        static::scopeExactProduct($query, $table, $productId);

        if (static::supportsPrintTechniqueColumn($table)) {
            $query->where('print_technique_slug', $printTechniqueSlug);
        } elseif ($printTechniqueSlug !== PrintTechniqueDimensionMultiplierTypes::SLUG_EMPRIME) {
            return;
        }

        $query->when($keptIds !== [], fn ($q) => $q->whereNotIn('id', $keptIds))->delete();
    }

    private static function supportsPrintTechniqueColumn(string $table): bool
    {
        return Schema::hasTable($table) && Schema::hasColumn($table, 'print_technique_slug');
    }

    /**
     * Yeni ürün formu için varsayılan şablon (id'siz).
     *
     * @return array<string, array<string, list<array<string, mixed>>>>
     */
    public static function loadTemplateForNewProduct(): array
    {
        return static::stripIds(static::loadGroupedForForm(null, fallbackToTemplate: false));
    }

    /**
     * @param  array<string, array<string, list<array<string, mixed>>>>  $loaded
     * @return array<string, array<string, list<array<string, mixed>>>>
     */
    public static function stripIds(array $loaded): array
    {
        foreach ($loaded as $slug => $groups) {
            foreach ($groups as $key => $rows) {
                $loaded[$slug][$key] = array_map(function (array $row): array {
                    unset($row['id']);

                    return $row;
                }, $rows);
            }
        }

        return $loaded;
    }

    private static function normalizeDecimal(mixed $value, float $fallback, int $decimals = 4): float
    {
        if (! is_numeric($value)) {
            return $fallback;
        }

        return max(0, round((float) $value, $decimals));
    }

    private static function normalizeFixedMultiplier(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        $numeric = str_replace(',', '.', $raw);
        if (is_numeric($numeric) && ! preg_match('/[a-zA-ZğüşıöçĞÜŞİÖÇ]/u', $raw)) {
            return (string) max(0, round((float) $numeric, 4));
        }

        return $raw;
    }
}
