<?php

namespace App\Models;

use App\Models\Concerns\FillsLocalizedNameFromCatalog;
use App\Models\Concerns\HasLocalizedName;
use App\Models\Concerns\SyncsLinkedProductVariationOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class InterfaceFabricTypeVariation extends Model
{
    use FillsLocalizedNameFromCatalog;
    use HasLocalizedName;
    use SyncsLinkedProductVariationOptions;

    protected $table = 'interface_fabric_type_variations';

    protected $fillable = [
        'name',
        'name_en',
        'name_it',
        'image_path',
        'detail_text',
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
        return 'fabric';
    }

    public function productVariationOptions()
    {
        return $this->hasMany(ProductVariationOption::class, 'interface_fabric_type_variation_id');
    }

    /** Bu kumaş türüne bağlı arayüz renk swatch'ları (Renk Varyasyonları, eski tek FK). */
    public function interfaceColorVariations()
    {
        return $this->hasMany(InterfaceColorVariation::class, 'interface_fabric_type_variation_id');
    }

    /** Renk Varyasyonları kayıtları (çoklu; pivot). */
    public function colorVariations()
    {
        return $this->belongsToMany(
            InterfaceColorVariation::class,
            'interface_color_variation_interface_fabric_type_variation',
            'interface_fabric_type_variation_id',
            'interface_color_variation_id',
        )->withTimestamps();
    }

    /** Bu kumaşın atandığı ürünler (çoklu; pivot). Yalnızca bu ürünlerde varyasyon seçeneği olur. */
    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'interface_fabric_type_variation_product',
            'interface_fabric_type_variation_id',
            'product_id',
        )->withTimestamps();
    }

    /** Mağaza / ürün varyasyonunda kullanım için aktif kayıtlar. */
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
        return Schema::hasTable('interface_fabric_type_variation_product');
    }

    /**
     * Yalnızca verilen ürüne açıkça atanmış kumaşlar.
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
     * Verilen üründe GİZLENECEK kumaş id'leri: bu ürüne atanmamış tüm kumaşlar.
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

    /** Bu kumaş verilen ürünün varyasyonlarında görünmeli mi? */
    public function isVisibleForProduct(?int $productId): bool
    {
        if (! static::productPivotTableExists() || $productId === null) {
            return false;
        }

        return $this->products()->whereKey($productId)->exists();
    }
}
