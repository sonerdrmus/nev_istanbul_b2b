<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interface_label_type_variations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image_path', 512)->nullable();
            $table->boolean('is_custom_print')->default(false);
            $table->boolean('position_front')->default(false);
            $table->boolean('position_back')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interface_label_type_variations');
    }
};
