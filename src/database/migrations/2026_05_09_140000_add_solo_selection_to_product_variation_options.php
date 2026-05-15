<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variation_options', function (Blueprint $table) {
            $table->boolean('solo_selection')->default(false)->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('product_variation_options', function (Blueprint $table) {
            $table->dropColumn('solo_selection');
        });
    }
};
