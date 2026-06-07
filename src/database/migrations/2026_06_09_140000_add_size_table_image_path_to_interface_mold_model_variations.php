<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interface_mold_model_variations')) {
            return;
        }

        if (! Schema::hasColumn('interface_mold_model_variations', 'size_table_image_path')) {
            Schema::table('interface_mold_model_variations', function (Blueprint $table) {
                $table->string('size_table_image_path')->nullable()->after('image_path');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('interface_mold_model_variations')
            || ! Schema::hasColumn('interface_mold_model_variations', 'size_table_image_path')) {
            return;
        }

        Schema::table('interface_mold_model_variations', function (Blueprint $table) {
            $table->dropColumn('size_table_image_path');
        });
    }
};
