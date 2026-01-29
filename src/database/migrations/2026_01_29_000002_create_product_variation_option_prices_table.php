<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variation_option_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variation_id')->constrained('product_variations')->cascadeOnDelete();
            $table->string('option_value', 255);
            $table->decimal('price_delta_try', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['product_variation_id', 'option_value'], 'pvop_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variation_option_prices');
    }
};

