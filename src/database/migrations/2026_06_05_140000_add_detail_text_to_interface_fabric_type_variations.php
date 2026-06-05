<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interface_fabric_type_variations', function (Blueprint $table) {
            if (! Schema::hasColumn('interface_fabric_type_variations', 'detail_text')) {
                $table->text('detail_text')->nullable()->after('image_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('interface_fabric_type_variations', function (Blueprint $table) {
            if (Schema::hasColumn('interface_fabric_type_variations', 'detail_text')) {
                $table->dropColumn('detail_text');
            }
        });
    }
};
