<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('variation_price_delta_total', 12, 2)->default(0)->after('subtotal');
            $table->json('variation_price_breakdown')->nullable()->after('variation_price_delta_total');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['variation_price_delta_total', 'variation_price_breakdown']);
        });
    }
};

