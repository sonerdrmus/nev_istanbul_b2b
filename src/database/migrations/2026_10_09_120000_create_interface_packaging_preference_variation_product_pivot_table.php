<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interface_packaging_preference_variations') || ! Schema::hasTable('products')) {
            return;
        }

        if (! Schema::hasTable('interface_packaging_preference_variation_product')) {
            Schema::create('interface_packaging_preference_variation_product', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('interface_packaging_preference_variation_id');
                $table->unsignedBigInteger('product_id');
                $table->timestamps();

                $table->unique(
                    ['interface_packaging_preference_variation_id', 'product_id'],
                    'pkg_pref_product_unique',
                );
                $table->foreign(
                    'interface_packaging_preference_variation_id',
                    'pkg_pref_var_product_fk',
                )->references('id')->on('interface_packaging_preference_variations')->cascadeOnDelete();
                $table->foreign('product_id', 'pkg_pref_product_fk')
                    ->references('id')->on('products')->cascadeOnDelete();
            });
        }

        $this->backfillFromExistingProductOptions();
    }

    public function down(): void
    {
        Schema::dropIfExists('interface_packaging_preference_variation_product');
    }

    /**
     * Mevcut ürün ambalaj seçeneklerini atama kabul eder. Aksi halde canlı katalog
     * bir sonraki senkrona kadar ambalajsız kalır.
     */
    private function backfillFromExistingProductOptions(): void
    {
        if (! Schema::hasTable('product_variation_options') || ! Schema::hasTable('product_variations')) {
            return;
        }

        if (! Schema::hasColumn('product_variation_options', 'interface_packaging_preference_variation_id')) {
            return;
        }

        $pairs = DB::table('product_variation_options as o')
            ->join('product_variations as v', 'v.id', '=', 'o.product_variation_id')
            ->join('interface_packaging_preference_variations as p', 'p.id', '=', 'o.interface_packaging_preference_variation_id')
            ->join('products as pr', 'pr.id', '=', 'v.product_id')
            ->where('v.type', 'packaging_type')
            ->whereNotNull('o.interface_packaging_preference_variation_id')
            ->select('o.interface_packaging_preference_variation_id', 'v.product_id')
            ->distinct()
            ->get();

        if ($pairs->isEmpty()) {
            return;
        }

        $now = now();
        foreach ($pairs->chunk(400) as $chunk) {
            DB::table('interface_packaging_preference_variation_product')->insertOrIgnore(
                $chunk->map(fn ($row): array => [
                    'interface_packaging_preference_variation_id' => (int) $row->interface_packaging_preference_variation_id,
                    'product_id' => (int) $row->product_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all(),
            );
        }
    }
};
