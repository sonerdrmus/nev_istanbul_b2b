<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interface_fabric_type_variations')) {
            return;
        }

        if (! Schema::hasColumn('interface_fabric_type_variations', 'price_multiplier')) {
            Schema::table('interface_fabric_type_variations', function (Blueprint $table) {
                $table->decimal('price_multiplier', 12, 4)->default(1)->after('detail_text');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('interface_fabric_type_variations')) {
            return;
        }

        if (Schema::hasColumn('interface_fabric_type_variations', 'price_multiplier')) {
            Schema::table('interface_fabric_type_variations', function (Blueprint $table) {
                $table->dropColumn('price_multiplier');
            });
        }
    }
};
