<?php

namespace App\Models;

use App\Models\Concerns\FillsLocalizedNameFromCatalog;
use App\Models\Concerns\HasLocalizedName;
use App\Models\Concerns\SyncsLinkedProductVariationOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class InterfaceMoldModelVariation extends Model
{
    use FillsLocalizedNameFromCatalog;
    use HasLocalizedName;
    use SyncsLinkedProductVariationOptions;

    protected $table = 'interface_mold_model_variations';

    protected $fillable = [
        'name',
        'name_en',
        'name_it',
        'image_path',
        'size_table_image_path',
        'price_multiplier',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_multiplier' => 'decimal:3',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function linkedProductVariationType(): string
    {
        return 'mold_model_type';
    }

    public function productVariationOptions()
    {
        return $this->hasMany(ProductVariationOption::class, 'interface_mold_model_variation_id');
    }

    /** Bu kalıbın atandığı ürünler (çoklu; pivot). Yalnızca bu ürünlerde varyasyon seçeneği olur. */
    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'interface_mold_model_variation_product',
            'interface_mold_model_variation_id',
            'product_id',
        )->withTimestamps();
    }

    /** Mağaza / ürün tarafında kullanım için aktif kayıtlar. */
    public static function forDisplay(): Collection
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public static function productPivotTableExists(): bool
    {
        return Schema::hasTable('interface_mold_model_variation_product');
    }

    /**
     * Yalnızca verilen ürüne açıkça atanmış kalıp modelleri.
     * Ürün id yoksa veya pivot yoksa sonuç boş kalır (ürün ataması zorunlu).
     */
    public function scopeVisibleForProduct(Builder $query, ?int $productId): Builder
    {
        if (! static::productPivotTableExists() || $productId === null) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereHas('products', fn (Builder $p) => $p->whereKey($productId));
    }

    /**
     * Verilen üründe GİZLENECEK kalıp model id'leri: bu ürüne atanmamış tüm kalıplar.
     *
     * @return array<int, int>
     */
    public static function hiddenIdsForProduct(?int $productId): array
    {
        if (! static::productPivotTableExists()) {
            return [];
        }

        return static::query()
            ->when(
                $productId !== null,
                fn (Builder $q) => $q->whereDoesntHave('products', fn (Builder $p) => $p->whereKey($productId)),
                fn (Builder $q) => $q,
            )
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    /** Bu kalıp verilen ürünün varyasyonlarında görünmeli mi? */
    public function isVisibleForProduct(?int $productId): bool
    {
        if (! static::productPivotTableExists() || $productId === null) {
            return false;
        }

        return $this->products()->whereKey($productId)->exists();
    }
}
