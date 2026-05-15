<?php

use App\Models\InterfaceColorVariation;
use App\Support\InterfaceColorSwatchGenerator;
use Database\Seeders\Catalog\UnassignedInterfaceColorItems;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Pantone tablosu hex renkleri → Renk Varyasyonları, Grup atanmamış.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interface_color_variations')) {
            return;
        }

        foreach (UnassignedInterfaceColorItems::records() as $row) {
            $absolute = storage_path('app/public/'.$row['image_path']);
            InterfaceColorSwatchGenerator::writePng($row['hex'], $absolute);

            InterfaceColorVariation::query()->updateOrCreate(
                ['image_path' => $row['image_path']],
                [
                    'name' => $row['name'],
                    'interface_fabric_type_variation_id' => null,
                    'sort_order' => $row['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('interface_color_variations')) {
            return;
        }

        $names = array_column(UnassignedInterfaceColorItems::records(), 'name');
        InterfaceColorVariation::query()->whereIn('name', $names)->delete();
    }
};
