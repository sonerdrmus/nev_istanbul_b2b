<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interface_packaging_settings')) {
            return;
        }

        if (! Schema::hasColumn('interface_packaging_settings', 'customizations_enabled')) {
            Schema::table('interface_packaging_settings', function (Blueprint $table): void {
                $table->boolean('customizations_enabled')->default(true)->after('barcode_image_path');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('interface_packaging_settings')) {
            return;
        }

        if (Schema::hasColumn('interface_packaging_settings', 'customizations_enabled')) {
            Schema::table('interface_packaging_settings', function (Blueprint $table): void {
                $table->dropColumn('customizations_enabled');
            });
        }
    }
};
