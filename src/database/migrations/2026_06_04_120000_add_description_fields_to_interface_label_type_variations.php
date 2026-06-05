<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interface_label_type_variations', function (Blueprint $table) {
            $table->boolean('ask_description')->default(false)->after('position_back');
            $table->string('description_title')->nullable()->after('ask_description');
        });
    }

    public function down(): void
    {
        Schema::table('interface_label_type_variations', function (Blueprint $table) {
            $table->dropColumn(['ask_description', 'description_title']);
        });
    }
};
