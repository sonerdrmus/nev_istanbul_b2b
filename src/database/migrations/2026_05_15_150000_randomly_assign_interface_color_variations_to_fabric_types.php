<?php

use App\Models\InterfaceColorVariation;
use App\Models\InterfaceFabricTypeVariation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Renk varyasyonlarını aktif kumaş türü gruplarına rastgele dağıtır.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interface_color_variations')
            || ! Schema::hasTable('interface_fabric_type_variations')) {
            return;
        }

        $fabricTypeIds = InterfaceFabricTypeVariation::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id')
            ->all();

        if ($fabricTypeIds === []) {
            return;
        }

        InterfaceColorVariation::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->each(function (InterfaceColorVariation $color) use ($fabricTypeIds): void {
                $color->update([
                    'interface_fabric_type_variation_id' => $fabricTypeIds[array_rand($fabricTypeIds)],
                ]);
            });
    }

    public function down(): void
    {
        if (! Schema::hasTable('interface_color_variations')) {
            return;
        }

        InterfaceColorVariation::query()->update([
            'interface_fabric_type_variation_id' => null,
        ]);
    }
};
