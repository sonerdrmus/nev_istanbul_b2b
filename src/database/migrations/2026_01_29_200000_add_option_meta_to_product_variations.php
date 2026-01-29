<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->json('option_meta')->nullable()->after('options_by_parent')->comment('option_value => { color: "#hex", image: "path" }');
        });
    }

    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropColumn('option_meta');
        });
    }
};
