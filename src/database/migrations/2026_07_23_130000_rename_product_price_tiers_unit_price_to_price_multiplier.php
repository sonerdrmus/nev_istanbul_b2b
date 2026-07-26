<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_price_tiers')) {
            return;
        }

        if (Schema::hasColumn('product_price_tiers', 'unit_price')
            && ! Schema::hasColumn('product_price_tiers', 'price_multiplier')) {
            Schema::table('product_price_tiers', function (Blueprint $table) {
                $table->renameColumn('unit_price', 'price_multiplier');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('product_price_tiers')) {
            return;
        }

        if (Schema::hasColumn('product_price_tiers', 'price_multiplier')
            && ! Schema::hasColumn('product_price_tiers', 'unit_price')) {
            Schema::table('product_price_tiers', function (Blueprint $table) {
                $table->renameColumn('price_multiplier', 'unit_price');
            });
        }
    }
};
