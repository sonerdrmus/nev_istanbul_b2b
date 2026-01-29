<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'type',
        'image',
        'depends_on',
        'options',
        'options_by_parent',
        'option_meta',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'options_by_parent' => 'array',
            'option_meta' => 'array',
        ];
    }

    /** Seçenek için renk/görsel meta (color: hex, image: path). */
    public function getOptionMeta(string $optionValue): ?array
    {
        $meta = $this->option_meta ?? [];
        if (! is_array($meta) || ! isset($meta[$optionValue])) {
            return null;
        }
        $m = $meta[$optionValue];
        return is_array($m) ? $m : null;
    }

    /** Okuma: list formatı object'e çevir (frontend + getOptionsForParentValue). Saklama: list (fiyatlarla). */
    public function getOptionsByParentAttribute($value): ?array
    {
        $raw = $this->getRawOriginal('options_by_parent');
        if ($raw === null) {
            return null;
        }
        $arr = is_string($raw) ? json_decode($raw, true) : $raw;
        if (! is_array($arr) || empty($arr)) {
            return null;
        }
        if (! array_is_list($arr) || ! isset($arr[0]['parent_value'])) {
            return $arr;
        }
        $out = [];
        foreach ($arr as $row) {
            $pv = $row['parent_value'] ?? null;
            $opts = $row['options'] ?? [];
            if ($pv === null || $pv === '' || ! is_array($opts)) {
                continue;
            }
            $vals = [];
            foreach ($opts as $o) {
                $v = is_array($o) ? ($o['option_value'] ?? null) : $o;
                if ($v !== null && $v !== '') {
                    $vals[] = (string) $v;
                }
            }
            if ($vals !== []) {
                $out[(string) $pv] = $vals;
            }
        }
        return $out ?: null;
    }

    /** Sync için ham list formatı (fiyatlarla). */
    public function getOptionsByParentRaw(): array
    {
        $raw = $this->getRawOriginal('options_by_parent');
        if ($raw === null) {
            return [];
        }
        $arr = is_string($raw) ? json_decode($raw, true) : $raw;
        return is_array($arr) && array_is_list($arr) ? $arr : [];
    }

    /** Seçilen üst değere göre seçenekleri döndürür (bağımlı varyasyonda). */
    public function getOptionsForParentValue(?string $parentValue): array
    {
        if (empty($this->depends_on) || $parentValue === null) {
            return $this->options ?? [];
        }
        $byParent = $this->options_by_parent ?? [];
        return is_array($byParent) && isset($byParent[$parentValue]) ? (array) $byParent[$parentValue] : [];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function optionPrices()
    {
        return $this->hasMany(ProductVariationOptionPrice::class, 'product_variation_id');
    }

    protected static function booted(): void
    {
        static::saving(function (ProductVariation $variation): void {
            if (! empty($variation->depends_on) && ($variation->options === null || $variation->options === [])) {
                $variation->options = [];
            }
            // options_by_parent list formatında (fiyatlarla) saklanır; okuma accessor ile object.
        });
    }
}
