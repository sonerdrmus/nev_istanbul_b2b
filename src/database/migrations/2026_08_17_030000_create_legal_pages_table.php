<?php

use Database\Seeders\LegalPageSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('title_it')->nullable();
            $table->longText('body');
            $table->longText('body_en')->nullable();
            $table->longText('body_it')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        (new LegalPageSeeder)->run();
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_pages');
    }
};
