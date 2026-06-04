<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->json('depends_on_option_ids')
                ->nullable()
                ->after('depends_on')
                ->comment('Bağlı varyasyondaki hangi seçeneklerde bu adım görünsün (boş = hepsi)');
        });
    }

    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropColumn('depends_on_option_ids');
        });
    }
};
