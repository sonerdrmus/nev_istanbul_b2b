<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('size_dimension_multipliers', function (Blueprint $table) {
            $table->string('fixed_multiplier', 64)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('size_dimension_multipliers', function (Blueprint $table) {
            $table->decimal('fixed_multiplier', 12, 4)->default(1)->change();
        });
    }
};
