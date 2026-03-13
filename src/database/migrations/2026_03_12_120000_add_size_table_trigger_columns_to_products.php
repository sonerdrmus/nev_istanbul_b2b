<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('size_table_erkek_variation')->nullable()->after('sort_order');
            $table->string('size_table_erkek_value')->nullable()->after('size_table_erkek_variation');
            $table->string('size_table_kadin_variation')->nullable()->after('size_table_erkek_value');
            $table->string('size_table_kadin_value')->nullable()->after('size_table_kadin_variation');
            $table->string('size_table_cocuk_variation')->nullable()->after('size_table_kadin_value');
            $table->string('size_table_cocuk_value')->nullable()->after('size_table_cocuk_variation');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'size_table_erkek_variation',
                'size_table_erkek_value',
                'size_table_kadin_variation',
                'size_table_kadin_value',
                'size_table_cocuk_variation',
                'size_table_cocuk_value',
            ]);
        });
    }
};
