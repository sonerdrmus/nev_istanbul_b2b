<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('color_dimension_multipliers');
        Schema::dropIfExists('quantity_dimension_multipliers');

        Schema::create('color_dimension_multipliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('color_count');
            $table->decimal('multiplier_price', 12, 4)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('color_count');
        });

        Schema::create('quantity_dimension_multipliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('quantity_from');
            $table->unsignedInteger('quantity_to');
            $table->decimal('multiplier_price', 12, 4)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['quantity_from', 'quantity_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quantity_dimension_multipliers');
        Schema::dropIfExists('color_dimension_multipliers');

        Schema::create('color_dimension_multipliers', function (Blueprint $table) {
            $table->id();
            $table->string('size_label');
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('auto_multiplier', 12, 4)->default(1);
            $table->string('fixed_multiplier', 64)->nullable();
            $table->decimal('extra_multiplier', 12, 4)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('quantity_dimension_multipliers', function (Blueprint $table) {
            $table->id();
            $table->string('size_label');
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('auto_multiplier', 12, 4)->default(1);
            $table->string('fixed_multiplier', 64)->nullable();
            $table->decimal('extra_multiplier', 12, 4)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
