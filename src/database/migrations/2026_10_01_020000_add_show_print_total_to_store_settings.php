<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('store_settings') || Schema::hasColumn('store_settings', 'show_print_total')) {
            return;
        }

        Schema::table('store_settings', function (Blueprint $table) {
            $table->boolean('show_print_total')->default(false)->after('show_price_multipliers');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('store_settings') || ! Schema::hasColumn('store_settings', 'show_print_total')) {
            return;
        }

        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn('show_print_total');
        });
    }
};
