<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('categories', 'show_in_top_menu')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->boolean('show_in_top_menu')->default(false)->after('is_active');
            });
        }
        if (! Schema::hasColumn('categories', 'top_menu_sort_order')) {
            Schema::table('categories', function (Blueprint $table) {
                $after = Schema::hasColumn('categories', 'show_in_top_menu') ? 'show_in_top_menu' : 'is_active';
                $table->unsignedInteger('top_menu_sort_order')->default(0)->after($after);
            });
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'top_menu_sort_order')) {
                $table->dropColumn('top_menu_sort_order');
            }
            if (Schema::hasColumn('categories', 'show_in_top_menu')) {
                $table->dropColumn('show_in_top_menu');
            }
        });
    }
};
