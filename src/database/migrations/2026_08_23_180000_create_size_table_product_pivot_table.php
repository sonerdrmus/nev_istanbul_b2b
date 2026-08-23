<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('size_tables') || ! Schema::hasTable('products')) {
            return;
        }

        if (Schema::hasTable('size_table_product')) {
            return;
        }

        Schema::create('size_table_product', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('size_table_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();

            $table->foreign('size_table_id', 'size_table_product_table_fk')
                ->references('id')
                ->on('size_tables')
                ->cascadeOnDelete();
            $table->foreign('product_id', 'size_table_product_product_fk')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();

            $table->unique(['size_table_id', 'product_id'], 'size_table_product_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('size_table_product');
    }
};
