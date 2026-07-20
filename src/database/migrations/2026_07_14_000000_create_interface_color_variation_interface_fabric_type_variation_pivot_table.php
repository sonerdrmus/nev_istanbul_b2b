<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interface_color_variations')) {
            return;
        }

        if (! Schema::hasTable('interface_fabric_type_variations')) {
            return;
        }

        if (Schema::hasTable('interface_color_variation_interface_fabric_type_variation')) {
            return;
        }

        Schema::create('interface_color_variation_interface_fabric_type_variation', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('interface_color_variation_id')
                ->constrained('interface_color_variations')
                ->cascadeOnDelete();
            $table->foreignId('interface_fabric_type_variation_id')
                ->constrained('interface_fabric_type_variations')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['interface_color_variation_id', 'interface_fabric_type_variation_id'], 'color_fabric_group_unique');
        });

        if (Schema::hasColumn('interface_color_variations', 'interface_fabric_type_variation_id')) {
            $legacyRows = DB::table('interface_color_variations')
                ->whereNotNull('interface_fabric_type_variation_id')
                ->select(['id', 'interface_fabric_type_variation_id'])
                ->get();

            foreach ($legacyRows as $row) {
                DB::table('interface_color_variation_interface_fabric_type_variation')->insertOrIgnore([
                    'interface_color_variation_id' => $row->id,
                    'interface_fabric_type_variation_id' => $row->interface_fabric_type_variation_id,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('interface_color_variation_interface_fabric_type_variation');
    }
};
