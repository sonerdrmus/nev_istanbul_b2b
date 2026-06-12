<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interface_certificate_variations')) {
            return;
        }

        if (! Schema::hasColumn('interface_certificate_variations', 'description')) {
            Schema::table('interface_certificate_variations', function (Blueprint $table) {
                $table->text('description')->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('interface_certificate_variations')) {
            return;
        }

        if (Schema::hasColumn('interface_certificate_variations', 'description')) {
            Schema::table('interface_certificate_variations', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
