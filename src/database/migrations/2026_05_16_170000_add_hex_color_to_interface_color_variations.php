<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interface_color_variations')) {
            return;
        }

        Schema::table('interface_color_variations', function (Blueprint $table) {
            if (! Schema::hasColumn('interface_color_variations', 'hex_color')) {
                $table->string('hex_color', 7)->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('interface_color_variations')) {
            return;
        }

        Schema::table('interface_color_variations', function (Blueprint $table) {
            if (Schema::hasColumn('interface_color_variations', 'hex_color')) {
                $table->dropColumn('hex_color');
            }
        });
    }
};
