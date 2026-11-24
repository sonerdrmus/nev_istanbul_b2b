<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_variation_options')) {
            return;
        }

        if (! Schema::hasColumn('product_variation_options', 'interface_product_model_variation_id')) {
            Schema::table('product_variation_options', function (Blueprint $table) {
                // after() Hostinger/MariaDB'de bazen sorun çıkarır; kolon sırası kritik değil.
                $table->unsignedBigInteger('interface_product_model_variation_id')->nullable();
            });
        }

        $connection = Schema::getConnection();
        if (! in_array($connection->getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        // Tip uyumu: bazı paylaşımlı hostlarda kolon signed kalabiliyor.
        try {
            DB::statement('ALTER TABLE `product_variation_options` MODIFY `interface_product_model_variation_id` BIGINT UNSIGNED NULL');
        } catch (\Throwable $e) {
            Log::warning('product_variation_options.interface_product_model_variation_id tip düzeltmesi atlandı', [
                'message' => $e->getMessage(),
            ]);
        }

        if (Schema::hasTable('interface_product_model_variations')) {
            try {
                DB::statement('ALTER TABLE `interface_product_model_variations` ENGINE=InnoDB');
            } catch (\Throwable $e) {
                // yoksay
            }
        }

        $foreignKeys = collect($connection->select(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            ['product_variation_options', 'interface_product_model_variation_id'],
        ))->pluck('CONSTRAINT_NAME');

        if ($foreignKeys->isNotEmpty() || ! Schema::hasTable('interface_product_model_variations')) {
            return;
        }

        // Önceki başarısız denemeden kalan index varsa temizle.
        try {
            $indexes = collect($connection->select('SHOW INDEX FROM `product_variation_options` WHERE Key_name = ?', [
                'pvo_interface_product_model_fk',
            ]));
            if ($indexes->isNotEmpty()) {
                DB::statement('ALTER TABLE `product_variation_options` DROP INDEX `pvo_interface_product_model_fk`');
            }
        } catch (\Throwable $e) {
            // yoksay
        }

        try {
            Schema::table('product_variation_options', function (Blueprint $table) {
                $table->foreign('interface_product_model_variation_id', 'pvo_interface_product_model_fk')
                    ->references('id')
                    ->on('interface_product_model_variations')
                    ->nullOnDelete();
            });
        } catch (\Throwable $e) {
            // Hostinger/MariaDB errno 150: uygulama kolon ile çalışır; FK zorunlu değil.
            Log::warning('pvo_interface_product_model_fk eklenemedi; kolon ile devam', [
                'message' => $e->getMessage(),
            ]);
            try {
                Schema::table('product_variation_options', function (Blueprint $table) {
                    $table->index('interface_product_model_variation_id', 'pvo_interface_product_model_idx');
                });
            } catch (\Throwable $indexError) {
                // yoksay
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('product_variation_options')
            || ! Schema::hasColumn('product_variation_options', 'interface_product_model_variation_id')) {
            return;
        }

        Schema::table('product_variation_options', function (Blueprint $table) {
            try {
                $table->dropForeign('pvo_interface_product_model_fk');
            } catch (\Throwable $e) {
                // yoksay
            }
            try {
                $table->dropIndex('pvo_interface_product_model_idx');
            } catch (\Throwable $e) {
                // yoksay
            }
            $table->dropColumn('interface_product_model_variation_id');
        });
    }
};
