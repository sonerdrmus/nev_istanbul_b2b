<?php

namespace App\Models;

use App\Models\Concerns\FillsLocalizedNameFromCatalog;
use App\Models\Concerns\HasLocalizedName;
use App\Models\Concerns\SyncsLinkedProductVariationOptions;
use App\Support\CatalogLabelTranslator;
use App\Support\LocaleContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SizeTable extends Model
{
    use FillsLocalizedNameFromCatalog;
    use HasLocalizedName;
    use SyncsLinkedProductVariationOptions;

    protected $fillable = [
        'name',
        'name_en',
        'name_it',
        'slug',
        'title',
        'title_en',
        'title_it',
        'trigger_variation_name',
        'trigger_option_value',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::saving(function (SizeTable $table): void {
            $source = trim((string) ($table->title ?: $table->name ?: ''));
            if ($source === '') {
                return;
            }
            $pair = CatalogLabelTranslator::fillPair($source, $table->title_en, $table->title_it);
            if (blank($table->title_en)) {
                $table->title_en = $pair['en'] !== '' ? $pair['en'] : null;
            }
            if (blank($table->title_it)) {
                $table->title_it = $pair['it'] !== '' ? $pair['it'] : null;
            }
        });
    }

    public function getLocalizedTitleAttribute(): string
    {
        $tr = trim((string) ($this->title ?: $this->name ?: ''));

        return LocaleContent::display(
            $tr,
            $this->title_en ?: $this->name_en,
            $this->title_it ?: $this->name_it,
        );
    }

    protected static function linkedProductVariationType(): string
    {
        return 'size_table';
    }

    public function productVariationOptions()
    {
        return $this->hasMany(ProductVariationOption::class, 'size_table_id');
    }

    public function columns()
    {
        return $this->hasMany(SizeTableColumn::class)->orderBy('sort_order');
    }

    /**
     * Bu tablonun atandığı ürünler (opsiyonel).
     * Boş bırakılırsa beden-tablosu varyasyonu olan tüm ürünlerde kullanılabilir.
     */
    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'size_table_product',
            'size_table_id',
            'product_id',
        )->withTimestamps();
    }

    public static function productPivotTableExists(): bool
    {
        return Schema::hasTable('size_table_product');
    }

    /**
     * Verilen üründe görünecek tablolar:
     * - hiç ürüne atanmamış (global) tablolar, veya
     * - açıkça bu ürüne atanmış tablolar.
     */
    public function scopeVisibleForProduct(Builder $query, ?int $productId): Builder
    {
        if (! static::productPivotTableExists()) {
            return $query;
        }

        if ($productId === null) {
            return $query->whereDoesntHave('products');
        }

        return $query->where(function (Builder $q) use ($productId): void {
            $q->whereDoesntHave('products')
                ->orWhereHas('products', fn (Builder $p) => $p->whereKey($productId));
        });
    }

    /**
     * Bu üründe GİZLENECEK tablo id'leri: ürün ataması olan ama bu ürüne atanmamış tablolar.
     *
     * @return array<int, int>
     */
    public static function hiddenIdsForProduct(?int $productId): array
    {
        if (! static::productPivotTableExists() || $productId === null) {
            return [];
        }

        return static::query()
            ->whereHas('products')
            ->whereDoesntHave('products', fn (Builder $p) => $p->whereKey($productId))
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    public function isVisibleForProduct(?int $productId): bool
    {
        if (! static::productPivotTableExists()) {
            return true;
        }

        if (! $this->products()->exists()) {
            return true;
        }

        if ($productId === null) {
            return false;
        }

        return $this->products()->whereKey($productId)->exists();
    }
}
