<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variation_options', function (Blueprint $table) {
            $table->decimal('price_delta', 12, 3)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('product_variation_options', function (Blueprint $table) {
            $table->decimal('price_delta', 12, 2)->default(0)->change();
        });
    }
};
