<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interface_delivery_method_sub_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('interface_delivery_method_variation_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('interface_delivery_method_variation_id', 'idm_sub_opts_delivery_fk')
                ->references('id')
                ->on('interface_delivery_method_variations')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interface_delivery_method_sub_options');
    }
};
