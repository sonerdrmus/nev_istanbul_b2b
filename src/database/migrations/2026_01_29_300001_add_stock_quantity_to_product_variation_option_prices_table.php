<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variation_option_prices', function (Blueprint $table) {
            $table->unsignedInteger('stock_quantity')->nullable()->after('price_delta_try')->comment('Varyasyon seçeneği stok (opsiyonel)');
        });
    }

    public function down(): void
    {
        Schema::table('product_variation_option_prices', function (Blueprint $table) {
            $table->dropColumn('stock_quantity');
        });
    }
};
