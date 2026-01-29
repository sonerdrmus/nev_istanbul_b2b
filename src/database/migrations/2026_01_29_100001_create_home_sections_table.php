<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->string('image_path')->nullable()->comment('Arka plan / görsel');
            $table->string('label')->nullable()->comment('Üst etiket, örn: Kampanya');
            $table->string('title')->nullable()->comment('Ana başlık, örn: Üst Giyim');
            $table->string('subtitle')->nullable()->comment('Alt metin, örn: Tişört, gömlek...');
            $table->string('button_text')->nullable()->comment('Buton metni, örn: İncele');
            $table->string('link_url')->nullable()->comment('Tıklanınca gidilecek URL');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_sections');
    }
};
