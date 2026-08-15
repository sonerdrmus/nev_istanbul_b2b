<?php

namespace App\Models;

use App\Models\Concerns\FillsLocalizedNameFromCatalog;
use App\Models\Concerns\HasLocalizedName;
use App\Models\Concerns\SyncsLinkedProductVariationOptions;
use App\Support\LocaleContent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class InterfaceLabelTypeVariation extends Model
{
    use FillsLocalizedNameFromCatalog;
    use HasLocalizedName;
    use SyncsLinkedProductVariationOptions;

    protected $table = 'interface_label_type_variations';

    protected $fillable = [
        'name',
        'name_en',
        'name_it',
        'image_path',
        'is_custom_print',
        'position_front',
        'position_back',
        'ask_description',
        'description_title',
        'description_title_en',
        'description_title_it',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_custom_print' => 'boolean',
            'position_front' => 'boolean',
            'position_back' => 'boolean',
            'ask_description' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function linkedProductVariationType(): string
    {
        return 'label_type';
    }

    public function productVariationOptions()
    {
        return $this->hasMany(ProductVariationOption::class, 'interface_label_type_variation_id');
    }

    public function getLocalizedDescriptionTitleAttribute(): string
    {
        return LocaleContent::display(
            $this->description_title,
            $this->description_title_en ?? null,
            $this->description_title_it ?? null,
        );
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
}
