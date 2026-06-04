<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_variation_options')
            || Schema::hasColumn('product_variation_options', 'interface_label_type_variation_id')) {
            return;
        }

        Schema::table('product_variation_options', function (Blueprint $table) {
            $table->foreignId('interface_label_type_variation_id')
                ->nullable()
                ->after('interface_fabric_type_variation_id')
                ->constrained('interface_label_type_variations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('product_variation_options')
            || ! Schema::hasColumn('product_variation_options', 'interface_label_type_variation_id')) {
            return;
        }

        Schema::table('product_variation_options', function (Blueprint $table) {
            $table->dropConstrainedForeignId('interface_label_type_variation_id');
        });
    }
};
