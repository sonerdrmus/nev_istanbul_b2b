<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('customization_choice_options') || Schema::hasColumn('customization_choice_options', 'image_size')) {
            return;
        }

        Schema::table('customization_choice_options', function (Blueprint $table) {
            $table->string('image_size', 20)->default('medium')->after('image_path');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('customization_choice_options') || ! Schema::hasColumn('customization_choice_options', 'image_size')) {
            return;
        }

        Schema::table('customization_choice_options', function (Blueprint $table) {
            $table->dropColumn('image_size');
        });
    }
};
