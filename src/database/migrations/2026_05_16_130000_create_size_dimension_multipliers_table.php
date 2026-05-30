<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('size_dimension_multipliers', function (Blueprint $table) {
            $table->id();
            $table->string('size_label');
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('auto_multiplier', 12, 4)->default(1);
            $table->decimal('fixed_multiplier', 12, 4)->default(1);
            $table->decimal('extra_multiplier', 12, 4)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('size_dimension_multipliers');
    }
};
