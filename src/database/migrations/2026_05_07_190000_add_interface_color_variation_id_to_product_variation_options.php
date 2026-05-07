<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variation_options', function (Blueprint $table) {
            $table->foreignId('interface_color_variation_id')
                ->nullable()
                ->after('option_color')
                ->constrained('interface_color_variations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('product_variation_options', function (Blueprint $table) {
            $table->dropForeign(['interface_color_variation_id']);
            $table->dropColumn('interface_color_variation_id');
        });
    }
};
