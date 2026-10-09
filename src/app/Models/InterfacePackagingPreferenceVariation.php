<?php

namespace App\Models;

use App\Models\Concerns\FillsLocalizedNameFromCatalog;
use App\Models\Concerns\HasLocalizedName;
use App\Models\Concerns\SyncsLinkedProductVariationOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class InterfacePackagingPreferenceVariation extends Model
{
    use FillsLocalizedNameFromCatalog;
    use HasLocalizedName;
    use SyncsLinkedProductVariationOptions;

    protected $table = 'interface_packaging_preference_variations';

    protected $fillable = [
        'name',
        'name_en',
        'name_it',
        'slug',
        'image_path',
        'requires_material',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'requires_material' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function linkedProductVariationType(): string
    {
        return 'packaging_type';
    }

    public function productVariationOptions()
    {
        return $this->hasMany(ProductVariationOption::class, 'interface_packaging_preference_variation_id');
    }

    /** Bu ambalajın atandığı ürünler. Yalnızca bu ürünlerde varyasyon seçeneği olur. */
    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'interface_packaging_preference_variation_product',
            'interface_packaging_preference_variation_id',
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
        return Schema::hasTable('interface_packaging_preference_variation_product');
    }

    /**
     * Yalnızca verilen ürüne açıkça atanmış ambalajlar.
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
     * Verilen üründe gizlenecek ambalaj id'leri: bu ürüne atanmamış tüm ambalajlar.
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

    public function isVisibleForProduct(?int $productId): bool
    {
        if (! static::productPivotTableExists() || $productId === null) {
            return false;
        }

        return $this->products()->whereKey($productId)->exists();
    }
}
