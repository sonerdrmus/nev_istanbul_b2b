<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('extra_variations')) {
            Schema::create('extra_variations', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('question');
                $table->string('answer_type', 32);
                $table->text('info_text')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('extra_variation_options')) {
            Schema::create('extra_variation_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('extra_variation_id')->constrained('extra_variations')->cascadeOnDelete();
                $table->string('label');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('extra_variation_assignments')) {
            Schema::create('extra_variation_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('extra_variation_id')->constrained('extra_variations')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('after_product_variation_id')->constrained('product_variations')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['extra_variation_id', 'product_id'], 'extra_variation_product_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('extra_variation_assignments');
        Schema::dropIfExists('extra_variation_options');
        Schema::dropIfExists('extra_variations');
    }
};
