<?php

use App\Models\InterfaceColorVariation;
use Database\Seeders\Catalog\RBasicInterfaceColorItems;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interface_color_variations')) {
            return;
        }

        foreach (RBasicInterfaceColorItems::records() as $row) {
            InterfaceColorVariation::query()->updateOrCreate(
                ['name' => $row['name']],
                [
                    'image_path' => $row['image_path'],
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

        $names = array_column(RBasicInterfaceColorItems::records(), 'name');
        InterfaceColorVariation::query()->whereIn('name', $names)->delete();
    }
};
