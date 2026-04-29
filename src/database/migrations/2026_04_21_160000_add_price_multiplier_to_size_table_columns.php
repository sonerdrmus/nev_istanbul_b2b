<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('size_table_columns', function (Blueprint $table) {
            $table->decimal('price_multiplier', 10, 4)->default(1)->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('size_table_columns', function (Blueprint $table) {
            $table->dropColumn('price_multiplier');
        });
    }
};
