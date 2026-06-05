<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_variations')
            || Schema::hasColumn('product_variations', 'is_final_variation_step')) {
            return;
        }

        Schema::table('product_variations', function (Blueprint $table) {
            $table->boolean('is_final_variation_step')->default(false)->after('replace_main_gallery_image');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('product_variations')
            || ! Schema::hasColumn('product_variations', 'is_final_variation_step')) {
            return;
        }

        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropColumn('is_final_variation_step');
        });
    }
};
