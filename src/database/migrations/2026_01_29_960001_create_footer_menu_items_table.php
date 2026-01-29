<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('footer_menu_group_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('url', 500)->default('#');
            $table->boolean('open_in_new_tab')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_menu_items');
    }
};
