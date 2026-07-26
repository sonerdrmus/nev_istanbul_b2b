<?php

use App\Models\Product;
use App\Models\ProductCustomizationRow;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_customization_rows') && Schema::hasTable('products')
            && ! Schema::hasTable('product_customization_row_product')) {
            Schema::create('product_customization_row_product', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('product_customization_row_id');
                $table->unsignedBigInteger('product_id');
                $table->timestamps();

                $table->foreign('product_customization_row_id', 'pcr_row_fk')
                    ->references('id')->on('product_customization_rows')->cascadeOnDelete();
                $table->foreign('product_id', 'pcr_product_fk')
                    ->references('id')->on('products')->cascadeOnDelete();
                $table->unique(['product_customization_row_id', 'product_id'], 'pcr_product_unique');
            });

            if (Schema::hasColumn('products', 'customization_enabled')) {
                $productIds = Product::query()
                    ->where('customization_enabled', true)
                    ->pluck('id')
                    ->all();
                $rowIds = ProductCustomizationRow::query()
                    ->where('is_active', true)
                    ->pluck('id')
                    ->all();

                $now = now();
                foreach ($rowIds as $rowId) {
                    foreach ($productIds as $productId) {
                        DB::table('product_customization_row_product')->insertOrIgnore([
                            'product_customization_row_id' => $rowId,
                            'product_id' => $productId,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }
        }

        $this->addProductIdColumn('size_dimension_multipliers');
        $this->addProductIdColumn('color_dimension_multipliers');
        $this->addProductIdColumn('quantity_dimension_multipliers');

        $this->replaceUnique(
            'color_dimension_multipliers',
            'color_dim_mult_print_color_unique',
            ['product_id', 'print_technique_slug', 'color_count'],
            'color_dim_mult_product_print_color_unique',
        );
        $this->replaceUnique(
            'quantity_dimension_multipliers',
            'qty_dim_mult_print_range_unique',
            ['product_id', 'print_technique_slug', 'quantity_from', 'quantity_to'],
            'qty_dim_mult_product_print_range_unique',
        );
    }

    private function addProductIdColumn(string $table): void
    {
        if (! Schema::hasTable($table) || Schema::hasColumn($table, 'product_id')) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($table): void {
            $blueprint->unsignedBigInteger('product_id')->nullable()->after('id');
            $blueprint->index('product_id');
            $blueprint->foreign('product_id', substr($table, 0, 12).'_product_fk')
                ->references('id')->on('products')->cascadeOnDelete();
        });
    }

    /**
     * @param  list<string>  $newColumns
     */
    private function replaceUnique(string $table, string $oldName, array $newColumns, string $newName): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        try {
            Schema::table($table, function (Blueprint $blueprint) use ($oldName): void {
                $blueprint->dropUnique($oldName);
            });
        } catch (\Throwable) {
            // already dropped / different name
        }

        try {
            Schema::table($table, function (Blueprint $blueprint) use ($newColumns, $newName): void {
                $blueprint->unique($newColumns, $newName);
            });
        } catch (\Throwable) {
            // unique may already exist
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_customization_row_product');

        foreach (['size_dimension_multipliers', 'color_dimension_multipliers', 'quantity_dimension_multipliers'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'product_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                if ($table === 'color_dimension_multipliers') {
                    try {
                        $blueprint->dropUnique('color_dim_mult_product_print_color_unique');
                    } catch (\Throwable) {
                    }
                }
                if ($table === 'quantity_dimension_multipliers') {
                    try {
                        $blueprint->dropUnique('qty_dim_mult_product_print_range_unique');
                    } catch (\Throwable) {
                    }
                }

                try {
                    $blueprint->dropForeign(substr($table, 0, 12).'_product_fk');
                } catch (\Throwable) {
                }
                $blueprint->dropColumn('product_id');
            });
        }
    }
};
