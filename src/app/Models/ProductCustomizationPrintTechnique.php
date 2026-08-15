<?php

namespace App\Models;

use App\Models\Concerns\FillsLocalizedNameFromCatalog;
use App\Models\Concerns\HasLocalizedName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProductCustomizationPrintTechnique extends Model
{
    use FillsLocalizedNameFromCatalog;
    use HasLocalizedName;

    protected $fillable = [
        'name',
        'name_en',
        'name_it',
        'slug',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /** @return Collection<int, static> */
    public static function activeOrdered(): Collection
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
