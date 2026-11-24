<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('store_settings') || Schema::hasColumn('store_settings', 'media_cdn_url')) {
            return;
        }

        Schema::table('store_settings', function (Blueprint $table) {
            $table->string('media_cdn_url', 500)->nullable()->after('show_print_card_total');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('store_settings') || ! Schema::hasColumn('store_settings', 'media_cdn_url')) {
            return;
        }

        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn('media_cdn_url');
        });
    }
};
