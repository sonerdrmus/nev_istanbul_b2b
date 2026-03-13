<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Renk, Beden vb.
            $table->string('type')->default('select'); // select, color, image
            $table->string('depends_on')->nullable(); // Bağlı olduğu varyasyon adı
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_variation_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variation_id')->constrained()->cascadeOnDelete();
            $table->string('option_value'); // Kırmızı, XL vb.
            $table->string('option_color', 20)->nullable(); // hex
            $table->string('option_image')->nullable(); // path
            $table->decimal('price_delta', 12, 2)->default(0);
            $table->unsignedInteger('stock_quantity')->nullable();
            $table->unsignedBigInteger('parent_option_id')->nullable(); // Bağlı seçenek (self FK aşağıda)
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('product_variation_options', function (Blueprint $table) {
            $table->foreign('parent_option_id')->references('id')->on('product_variation_options')->nullOnDelete();
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

    public function down(): void
    {
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

        Schema::dropIfExists('product_variation_options');
        Schema::dropIfExists('product_variations');
    }
};
