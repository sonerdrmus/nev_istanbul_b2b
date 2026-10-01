<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('customization_choices')) {
            Schema::create('customization_choices', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->boolean('allows_multiple')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('customization_choice_options')) {
            Schema::create('customization_choice_options', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customization_choice_id');
                $table->foreign('customization_choice_id', 'cc_opt_choice_fk')
                    ->references('id')->on('customization_choices')->cascadeOnDelete();
                $table->string('label');
                $table->string('image_path')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('customization_choice_assignments')) {
            Schema::create('customization_choice_assignments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customization_choice_id');
                $table->foreign('customization_choice_id', 'cc_assign_choice_fk')
                    ->references('id')->on('customization_choices')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->unsignedBigInteger('after_product_variation_id');
                $table->foreign('after_product_variation_id', 'cc_assign_after_fk')
                    ->references('id')->on('product_variations')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['customization_choice_id', 'product_id'], 'customization_choice_product_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('customization_choice_assignments');
        Schema::dropIfExists('customization_choice_options');
        Schema::dropIfExists('customization_choices');
    }
};
