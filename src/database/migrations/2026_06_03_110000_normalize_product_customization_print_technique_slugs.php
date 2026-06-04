<?php

use App\Support\PrintTechniqueSlugResolver;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_customization_print_techniques')) {
            return;
        }

        $rows = DB::table('product_customization_print_techniques')->get(['id', 'slug']);

        foreach ($rows as $row) {
            $canonical = PrintTechniqueSlugResolver::canonical((string) $row->slug);
            if ($canonical === (string) $row->slug) {
                continue;
            }

            $exists = DB::table('product_customization_print_techniques')
                ->where('slug', $canonical)
                ->where('id', '!=', $row->id)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('product_customization_print_techniques')
                ->where('id', $row->id)
                ->update(['slug' => $canonical]);

            if (Schema::hasTable('product_customization_rows')) {
                DB::table('product_customization_rows')
                    ->where('default_print_technique_slug', $row->slug)
                    ->update(['default_print_technique_slug' => $canonical]);
            }

            if (Schema::hasTable('product_customization_settings')) {
                DB::table('product_customization_settings')
                    ->where('default_print_technique_slug', $row->slug)
                    ->update(['default_print_technique_slug' => $canonical]);
            }
        }
    }

    public function down(): void
    {
        // Slug normalizasyonu geri alınmaz.
    }
};
