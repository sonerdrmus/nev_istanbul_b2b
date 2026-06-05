<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variation_options', function (Blueprint $table) {
            if (! Schema::hasColumn('product_variation_options', 'info_text')) {
                $table->text('info_text')->nullable()->after('option_value');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_variation_options', function (Blueprint $table) {
            if (Schema::hasColumn('product_variation_options', 'info_text')) {
                $table->dropColumn('info_text');
            }
        });
    }
};
