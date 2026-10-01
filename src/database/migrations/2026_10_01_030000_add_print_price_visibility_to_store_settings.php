<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('store_settings')) {
            return;
        }

        if (! Schema::hasColumn('store_settings', 'show_print_price')) {
            Schema::table('store_settings', function (Blueprint $table) {
                $table->boolean('show_print_price')->default(false)->after('show_print_total');
            });
        }

        if (! Schema::hasColumn('store_settings', 'show_print_card_total')) {
            Schema::table('store_settings', function (Blueprint $table) {
                $table->boolean('show_print_card_total')->default(false)->after('show_print_price');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('store_settings')) {
            return;
        }

        if (Schema::hasColumn('store_settings', 'show_print_card_total')) {
            Schema::table('store_settings', function (Blueprint $table) {
                $table->dropColumn('show_print_card_total');
            });
        }

        if (Schema::hasColumn('store_settings', 'show_print_price')) {
            Schema::table('store_settings', function (Blueprint $table) {
                $table->dropColumn('show_print_price');
            });
        }
    }
};
