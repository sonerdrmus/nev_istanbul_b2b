<?php

namespace App\Models;

use App\Support\CatalogLabelTranslator;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'type',
        'depends_on',
        'depends_on_option_ids',
        'sort_order',
        'replace_main_gallery_image',
        'allows_multiple',
        'solo_option_value',
        'info_text',
    ];

    protected function casts(): array
    {
        return [
            'replace_main_gallery_image' => 'boolean',
            'allows_multiple' => 'boolean',
            'depends_on_option_ids' => 'array',
        ];
    }

    public function getDisplayNameAttribute(): string
    {
        return CatalogLabelTranslator::label($this->name);
    }

    /** Bağlı varyasyondaki hangi seçeneklerde bu adım görünsün (boş = üst varyasyonda herhangi bir seçim). */
    public function getDependsOnOptionIdsList(): array
    {
        $ids = $this->depends_on_option_ids ?? [];
        if (! is_array($ids)) {
            return [];
        }

        return array_values(array_filter(array_map('intval', $ids)));
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function options()
    {
        return $this->hasMany(ProductVariationOption::class, 'product_variation_id')->orderBy('sort_order');
    }

    /**
     * Bağlı varyasyonda, seçilen üst seçeneğe göre gösterilecek seçenekleri döndürür.
     * parent_option_id null olanlar tüm üst seçeneklerde gösterilir.
     *
     * @param  int|null  $parentOptionId  Üst varyasyondaki seçilen seçeneğin id'si (bağlı değilse null)
     * @return \Illuminate\Database\Eloquent\Collection<int, ProductVariationOption>
     */
    public function getOptionsForParentOptionId(?int $parentOptionId): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->options();

        if ($parentOptionId === null) {
            return $query->whereNull('parent_option_id')->get();
        }

        return $query->where(function ($q) use ($parentOptionId) {
            $q->whereNull('parent_option_id')->orWhere('parent_option_id', $parentOptionId);
        })->get();
    }

    /** Mağazada tek başına seçim (solo) olarak işaretlenecek seçenek metniyle eşleşiyor mu? */
    public function optionValueIsSoloChoice(?string $optionValue): bool
    {
        $solo = trim((string) ($this->solo_option_value ?? ''));
        if ($solo === '' || $optionValue === null || $optionValue === '') {
            return false;
        }

        return mb_strtolower(trim($optionValue)) === mb_strtolower($solo);
    }
}
