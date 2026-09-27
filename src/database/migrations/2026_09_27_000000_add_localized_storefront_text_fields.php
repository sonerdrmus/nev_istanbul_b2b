<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'products' => ['description_it' => 'longText', 'meta_title_it' => 'string', 'meta_description_it' => 'string512'],
            'product_variations' => ['info_text_en' => 'text', 'info_text_it' => 'text'],
            'product_variation_options' => ['info_text_en' => 'text', 'info_text_it' => 'text'],
            'footer_menu_groups' => ['title_en' => 'string', 'title_it' => 'string'],
            'footer_menu_items' => ['label_en' => 'string', 'label_it' => 'string'],
        ];

        foreach ($columns as $tableName => $tableColumns) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName, $tableColumns): void {
                foreach ($tableColumns as $column => $type) {
                    if (! Schema::hasColumn($tableName, $column)) {
                        if ($type === 'string512') {
                            $table->string($column, 512)->nullable();
                        } else {
                            $table->{$type}($column)->nullable();
                        }
                    }
                }
            });
        }
    }

    public function down(): void
    {
        $columns = [
            'products' => ['description_it', 'meta_title_it', 'meta_description_it'],
            'product_variations' => ['info_text_en', 'info_text_it'],
            'product_variation_options' => ['info_text_en', 'info_text_it'],
            'footer_menu_groups' => ['title_en', 'title_it'],
            'footer_menu_items' => ['label_en', 'label_it'],
        ];

        foreach ($columns as $tableName => $tableColumns) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            foreach ($tableColumns as $column) {
                if (Schema::hasColumn($tableName, $column)) {
                    Schema::table($tableName, fn (Blueprint $table) => $table->dropColumn($column));
                }
            }
        }
    }
};