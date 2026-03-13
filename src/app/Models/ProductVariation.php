<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'type',
        'depends_on',
        'sort_order',
    ];

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
}
