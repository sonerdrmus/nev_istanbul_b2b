<?php

use App\Models\InterfaceColorVariation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Son eklenen hex renkler (#FFFFFF vb.) dışındaki eski Renk Varyasyonları kayıtlarını kaldırır.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interface_color_variations')) {
            return;
        }

        $legacy = InterfaceColorVariation::query()
            ->where('name', 'not like', '#%')
            ->get();

        foreach ($legacy as $record) {
            $path = $record->image_path;
            if (is_string($path) && $path !== '' && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $record->delete();
        }
    }

    public function down(): void
    {
        // Geri alınamaz; eski kayıtlar R BASIC seeder / migration ile yeniden yüklenebilir.
    }
};
