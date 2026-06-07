<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_variation_options')) {
            return;
        }

        if (! Schema::hasColumn('product_variation_options', 'interface_mold_model_variation_id')) {
            Schema::table('product_variation_options', function (Blueprint $table) {
                $table->unsignedBigInteger('interface_mold_model_variation_id')
                    ->nullable()
                    ->after('interface_delivery_method_variation_id');
            });
        }

        $foreignKeys = collect(Schema::getConnection()->select(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            ['product_variation_options', 'interface_mold_model_variation_id'],
        ))->pluck('CONSTRAINT_NAME');

        if ($foreignKeys->isEmpty() && Schema::hasTable('interface_mold_model_variations')) {
            Schema::table('product_variation_options', function (Blueprint $table) {
                $table->foreign('interface_mold_model_variation_id', 'pvo_interface_mold_model_fk')
                    ->references('id')
                    ->on('interface_mold_model_variations')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('product_variation_options')
            || ! Schema::hasColumn('product_variation_options', 'interface_mold_model_variation_id')) {
            return;
        }

        Schema::table('product_variation_options', function (Blueprint $table) {
            $table->dropForeign('pvo_interface_mold_model_fk');
            $table->dropColumn('interface_mold_model_variation_id');
        });
    }
};
