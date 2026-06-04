<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variation_options', function (Blueprint $table) {
            $table->foreignId('size_table_id')
                ->nullable()
                ->after('interface_fabric_type_variation_id')
                ->constrained('size_tables')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('product_variation_options', function (Blueprint $table) {
            $table->dropConstrainedForeignId('size_table_id');
        });
    }
};
