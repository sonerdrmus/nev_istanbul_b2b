<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interface_color_variations')) {
            return;
        }

        Schema::table('interface_color_variations', function (Blueprint $table) {
            if (Schema::hasColumn('interface_color_variations', 'interface_fabric_type_variation_id')) {
                return;
            }

            $table->foreignId('interface_fabric_type_variation_id')
                ->nullable()
                ->after('id')
                ->constrained('interface_fabric_type_variations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('interface_color_variations')) {
            return;
        }

        Schema::table('interface_color_variations', function (Blueprint $table) {
            if (! Schema::hasColumn('interface_color_variations', 'interface_fabric_type_variation_id')) {
                return;
            }

            $table->dropForeign(['interface_fabric_type_variation_id']);
            $table->dropColumn('interface_fabric_type_variation_id');
        });
    }
};
