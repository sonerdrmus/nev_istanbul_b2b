<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('size_tables', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Erkek, Kadın, Çocuk
            $table->string('slug', 50)->unique(); // erkek, kadin, cocuk
            $table->string('title')->nullable(); // Başlık (BEDEN TABLOSU (ERKEK) vb.)
            $table->string('trigger_variation_name')->nullable(); // Hangi varyasyon seçildiğinde
            $table->string('trigger_option_value')->nullable(); // Hangi seçenek değerinde
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('size_table_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('size_table_id')->constrained()->cascadeOnDelete();
            $table->string('size_value', 50); // XS, S, 98, 104 vb.
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('size_table_columns');
        Schema::dropIfExists('size_tables');
    }
};
