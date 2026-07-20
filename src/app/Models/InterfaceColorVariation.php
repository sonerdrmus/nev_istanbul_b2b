<?php

namespace App\Models;

use App\Models\Concerns\SyncsLinkedProductVariationOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class InterfaceColorVariation extends Model
{
    use SyncsLinkedProductVariationOptions;

    protected $table = 'interface_color_variations';

    protected $fillable = [
        'interface_fabric_type_variation_id',
        'name',
        'hex_color',
        'image_path',
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

    protected static function linkedProductVariationType(): string
    {
        return 'color';
    }

    public function productVariationOptions()
    {
        return $this->hasMany(ProductVariationOption::class, 'interface_color_variation_id');
    }

    /** Gruplama: Kumaş Türü Varyasyonları kaydı (admin listede grup adı buradan gelir). */
    public function fabricTypeVariation()
    {
        return $this->belongsTo(InterfaceFabricTypeVariation::class, 'interface_fabric_type_variation_id');
    }

    /** Kumaş türü grupları (çoklu; pivot). */
    public function fabricTypeVariations()
    {
        return $this->belongsToMany(
            InterfaceFabricTypeVariation::class,
            'interface_color_variation_interface_fabric_type_variation',
            'interface_color_variation_id',
            'interface_fabric_type_variation_id',
        )->withTimestamps();
    }

    /** Mağazada kumaş filtresi için tüm grup kimlikleri (pivot + eski tek FK). */
    public function resolveFabricTypeVariationIdsForStore(): array
    {
        if (! $this->relationLoaded('fabricTypeVariations')) {
            $this->load('fabricTypeVariations');
        }

        $ids = $this->fabricTypeVariations
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        if ($this->interface_fabric_type_variation_id !== null) {
            $legacyId = (int) $this->interface_fabric_type_variation_id;
            if (! in_array($legacyId, $ids, true)) {
                $ids[] = $legacyId;
            }
        }

        return array_values(array_unique($ids));
    }

    /** Mağaza / arayüzde gösterim sırasıyla aktif kayıtlar. */
    public static function forDisplay(): Collection
    {
        return static::query()
            ->where('is_active', true)
            ->whereNotNull('image_path')
            ->where('image_path', '!=', '')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
