<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->string('depends_on')->nullable()->after('type'); // Bağlı olduğu varyasyon adı (örn: Erkek/Bayan)
            $table->json('options_by_parent')->nullable()->after('options'); // {"Erkek": ["Sarı", "Mavi"], "Bayan": ["Pembe", "Mor"]}
        });
    }

    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropColumn(['depends_on', 'options_by_parent']);
        });
    }
};
