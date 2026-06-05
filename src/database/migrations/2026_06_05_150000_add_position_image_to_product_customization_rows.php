<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_customization_rows', function (Blueprint $table) {
            $table->string('position_image', 512)->nullable()->after('position_name');
        });
    }

    public function down(): void
    {
        Schema::table('product_customization_rows', function (Blueprint $table) {
            $table->dropColumn('position_image');
        });
    }
};
