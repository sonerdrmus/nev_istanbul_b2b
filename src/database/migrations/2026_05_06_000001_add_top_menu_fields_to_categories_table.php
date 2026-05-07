<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('show_in_top_menu')->default(false)->after('is_active');
            $table->unsignedInteger('top_menu_sort_order')->default(0)->after('show_in_top_menu');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['show_in_top_menu', 'top_menu_sort_order']);
        });
    }
};
