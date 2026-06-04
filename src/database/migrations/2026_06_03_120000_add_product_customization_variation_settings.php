<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('customization_enabled')->default(true)->after('size_table_trigger_variation');
            $table->string('customization_trigger_variation')->nullable()->after('customization_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['customization_enabled', 'customization_trigger_variation']);
        });
    }
};
