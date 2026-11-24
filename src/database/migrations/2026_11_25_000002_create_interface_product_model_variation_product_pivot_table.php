<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interface_product_model_variations') || ! Schema::hasTable('products')) {
            return;
        }

        if (Schema::hasTable('interface_product_model_variation_product')) {
            return;
        }

        Schema::create('interface_product_model_variation_product', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('interface_product_model_variation_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();

            $table->foreign('interface_product_model_variation_id', 'product_model_product_model_fk')
                ->references('id')
                ->on('interface_product_model_variations')
                ->cascadeOnDelete();
            $table->foreign('product_id', 'product_model_product_product_fk')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();

            $table->unique(['interface_product_model_variation_id', 'product_id'], 'product_model_product_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interface_product_model_variation_product');
    }
};
