<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('product_variation_option_prices');
        Schema::dropIfExists('product_variations');

        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (Schema::hasColumn('order_items', 'variation_data')) {
                    $table->dropColumn('variation_data');
                }
                if (Schema::hasColumn('order_items', 'variation_price_delta_total')) {
                    $table->dropColumn('variation_price_delta_total');
                }
                if (Schema::hasColumn('order_items', 'variation_price_breakdown')) {
                    $table->dropColumn('variation_price_breakdown');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('select');
            $table->string('depends_on')->nullable();
            $table->json('options')->nullable();
            $table->json('options_by_parent')->nullable();
            $table->json('option_meta')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_variation_option_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variation_id')->constrained()->cascadeOnDelete();
            $table->string('option_value');
            $table->decimal('price_delta_try', 12, 2)->default(0);
            $table->unsignedInteger('stock_quantity')->nullable();
            $table->timestamps();
        });

        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (! Schema::hasColumn('order_items', 'variation_data')) {
                    $table->json('variation_data')->nullable()->after('subtotal');
                }
                if (! Schema::hasColumn('order_items', 'variation_price_delta_total')) {
                    $table->decimal('variation_price_delta_total', 12, 2)->default(0)->after('variation_data');
                }
                if (! Schema::hasColumn('order_items', 'variation_price_breakdown')) {
                    $table->json('variation_price_breakdown')->nullable()->after('variation_price_delta_total');
                }
            });
        }
    }
};
