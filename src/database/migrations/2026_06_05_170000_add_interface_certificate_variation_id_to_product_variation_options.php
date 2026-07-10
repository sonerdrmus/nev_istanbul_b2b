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

        if (! Schema::hasColumn('product_variation_options', 'interface_certificate_variation_id')) {
            Schema::table('product_variation_options', function (Blueprint $table) {
                $table->unsignedBigInteger('interface_certificate_variation_id')
                    ->nullable()
                    ->after('interface_packaging_preference_variation_id');
            });
        }

        $connection = Schema::getConnection();
        if (! in_array($connection->getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        $foreignKeys = collect($connection->select(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            ['product_variation_options', 'interface_certificate_variation_id'],
        ))->pluck('CONSTRAINT_NAME');

        if ($foreignKeys->isEmpty()) {
            Schema::table('product_variation_options', function (Blueprint $table) {
                $table->foreign('interface_certificate_variation_id', 'pvo_interface_certificate_variation_fk')
                    ->references('id')
                    ->on('interface_certificate_variations')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('product_variation_options')
            || ! Schema::hasColumn('product_variation_options', 'interface_certificate_variation_id')) {
            return;
        }

        Schema::table('product_variation_options', function (Blueprint $table) {
            $table->dropForeign('pvo_interface_certificate_variation_fk');
            $table->dropColumn('interface_certificate_variation_id');
        });
    }
};
