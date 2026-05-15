<?php

use App\Models\InterfaceColorVariation;
use Database\Seeders\Catalog\UnassignedInterfaceColorItems;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Hex kodlu Renk Varyasyonu adlarını Türkçe renk adlarına çevirir (örn. Beyaz).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interface_color_variations')) {
            return;
        }

        foreach (UnassignedInterfaceColorItems::records() as $row) {
            InterfaceColorVariation::query()
                ->where('image_path', $row['image_path'])
                ->update(['name' => $row['name']]);
        }

        // Eski kayıtlar ad olarak hex tutuyordu; görsel yolu eşleşmeyenleri de güncelle.
        foreach (UnassignedInterfaceColorItems::hexCodesInOrder() as $hex) {
            $imagePath = UnassignedInterfaceColorItems::imagePathForHex($hex);
            $name = UnassignedInterfaceColorItems::displayNameForHex($hex);

            InterfaceColorVariation::query()
                ->where('name', $hex)
                ->where('image_path', $imagePath)
                ->update(['name' => $name]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('interface_color_variations')) {
            return;
        }

        foreach (UnassignedInterfaceColorItems::records() as $row) {
            InterfaceColorVariation::query()
                ->where('image_path', $row['image_path'])
                ->update(['name' => $row['hex']]);
        }
    }
};
