<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_customization_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('max_color_count')->default(7);
            $table->string('default_print_technique_slug', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('product_customization_print_techniques', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 64)->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_customization_rows', function (Blueprint $table) {
            $table->id();
            $table->string('position_name');
            $table->decimal('default_width', 8, 2)->nullable();
            $table->decimal('default_height', 8, 2)->nullable();
            $table->unsignedTinyInteger('default_color_count')->default(3);
            $table->string('default_print_technique_slug', 64)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_customization_rows');
        Schema::dropIfExists('product_customization_print_techniques');
        Schema::dropIfExists('product_customization_settings');
    }
};
