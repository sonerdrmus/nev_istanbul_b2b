<?php

namespace App\Models;

use App\Models\Concerns\FillsLocalizedNameFromCatalog;
use App\Support\LocaleContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ProductCustomizationRow extends Model
{
    use FillsLocalizedNameFromCatalog;

    protected $fillable = [
        'position_name',
        'position_name_en',
        'position_name_it',
        'position_image',
        'default_width',
        'default_height',
        'default_color_count',
        'default_print_technique_slug',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'default_width' => 'decimal:2',
            'default_height' => 'decimal:2',
            'default_color_count' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function localizedNameSourceAttribute(): string
    {
        return 'position_name';
    }

    public function getLocalizedPositionNameAttribute(): string
    {
        return LocaleContent::display($this->position_name, $this->position_name_en, $this->position_name_it);
    }

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_customization_row_product',
            'product_customization_row_id',
            'product_id',
        )->withTimestamps();
    }

    public static function productPivotTableExists(): bool
    {
        return Schema::hasTable('product_customization_row_product');
    }

    /** Yalnızca verilen ürüne açıkça atanmış satırlar. */
    public function scopeVisibleForProduct(Builder $query, ?int $productId): Builder
    {
        if (! static::productPivotTableExists() || $productId === null) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereHas('products', fn (Builder $p) => $p->whereKey($productId));
    }

    /** @return Collection<int, static> */
    public static function activeOrdered(?int $productId = null): Collection
    {
        $query = static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($productId !== null && static::productPivotTableExists()) {
            $query->visibleForProduct($productId);
        } elseif (static::productPivotTableExists()) {
            // Pivot varken ürün belirtilmeden mağaza satırı istenmez.
            $query->whereRaw('0 = 1');
        }

        return $query->get();
    }
}
