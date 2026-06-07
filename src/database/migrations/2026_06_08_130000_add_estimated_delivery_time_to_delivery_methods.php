<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interface_delivery_method_variations')) {
            return;
        }

        if (! Schema::hasColumn('interface_delivery_method_variations', 'estimated_delivery_time')) {
            Schema::table('interface_delivery_method_variations', function (Blueprint $table) {
                $table->string('estimated_delivery_time', 255)->nullable()->after('description');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('interface_delivery_method_variations')) {
            return;
        }

        if (Schema::hasColumn('interface_delivery_method_variations', 'estimated_delivery_time')) {
            Schema::table('interface_delivery_method_variations', function (Blueprint $table) {
                $table->dropColumn('estimated_delivery_time');
            });
        }
    }
};
