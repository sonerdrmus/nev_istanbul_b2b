<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table): void {
                if (! Schema::hasColumn('products', 'name_en')) {
                    $table->string('name_en')->nullable()->after('name');
                }
                if (! Schema::hasColumn('products', 'description_en')) {
                    $table->longText('description_en')->nullable()->after('description');
                }
                if (! Schema::hasColumn('products', 'meta_title_en')) {
                    $table->string('meta_title_en')->nullable()->after('meta_title');
                }
                if (! Schema::hasColumn('products', 'meta_description_en')) {
                    $table->string('meta_description_en', 512)->nullable()->after('meta_description');
                }
            });
        }

        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table): void {
                if (! Schema::hasColumn('categories', 'name_en')) {
                    $table->string('name_en')->nullable()->after('name');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table): void {
                foreach (['name_en', 'description_en', 'meta_title_en', 'meta_description_en'] as $col) {
                    if (Schema::hasColumn('products', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('categories') && Schema::hasColumn('categories', 'name_en')) {
            Schema::table('categories', function (Blueprint $table): void {
                $table->dropColumn('name_en');
            });
        }
    }
};
