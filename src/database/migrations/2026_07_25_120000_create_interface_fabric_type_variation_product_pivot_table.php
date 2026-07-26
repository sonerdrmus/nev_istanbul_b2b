<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interface_fabric_type_variations')) {
            return;
        }

        if (! Schema::hasTable('products')) {
            return;
        }

        if (Schema::hasTable('interface_fabric_type_variation_product')) {
            return;
        }

        Schema::create('interface_fabric_type_variation_product', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('interface_fabric_type_variation_id')
                ->constrained('interface_fabric_type_variations')
                ->cascadeOnDelete();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['interface_fabric_type_variation_id', 'product_id'], 'fabric_type_product_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interface_fabric_type_variation_product');
    }
};
