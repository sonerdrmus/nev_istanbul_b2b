<?php

use App\Support\CatalogLocaleBackfill;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Display-only EN/IT name columns. Canonical TR name/option_value/slug/trigger fields stay unchanged.
     */
    public function up(): void
    {
        $this->addPair('products', 'name', 'name');
        $this->addPair('categories', 'name', 'name');
        $this->addPair('product_variations', 'name', 'name');
        $this->addPair('product_variation_options', 'option_value', 'option_value');
        $this->addPair('interface_fabric_type_variations', 'name', 'name');
        $this->addPair('interface_color_variations', 'name', 'name');
        $this->addPair('interface_certificate_variations', 'name', 'name');
        $this->addPair('interface_mold_model_variations', 'name', 'name');
        $this->addPair('interface_delivery_method_variations', 'name', 'name');
        $this->addPair('interface_delivery_method_sub_options', 'name', 'name');
        $this->addPair('interface_packaging_preference_variations', 'name', 'name');
        $this->addPair('interface_label_type_variations', 'name', 'name');
        $this->addPair('interface_label_type_variations', 'description_title', 'description_title');
        $this->addPair('interface_packaging_materials', 'name', 'name');
        $this->addPair('interface_packaging_customizations', 'name', 'name');
        $this->addPair('size_tables', 'name', 'name');
        $this->addPair('size_tables', 'title', 'title');
        $this->addPair('product_customization_print_techniques', 'name', 'name');
        $this->addPair('product_customization_rows', 'position_name', 'position_name');

        CatalogLocaleBackfill::all();
    }

    public function down(): void
    {
        foreach ([
            'products' => ['name_it'],
            'categories' => ['name_it'],
            'product_variations' => ['name_en', 'name_it'],
            'product_variation_options' => ['option_value_en', 'option_value_it'],
            'interface_fabric_type_variations' => ['name_en', 'name_it'],
            'interface_color_variations' => ['name_en', 'name_it'],
            'interface_certificate_variations' => ['name_en', 'name_it'],
            'interface_mold_model_variations' => ['name_en', 'name_it'],
            'interface_delivery_method_variations' => ['name_en', 'name_it'],
            'interface_delivery_method_sub_options' => ['name_en', 'name_it'],
            'interface_packaging_preference_variations' => ['name_en', 'name_it'],
            'interface_label_type_variations' => ['name_en', 'name_it', 'description_title_en', 'description_title_it'],
            'interface_packaging_materials' => ['name_en', 'name_it'],
            'interface_packaging_customizations' => ['name_en', 'name_it'],
            'size_tables' => ['name_en', 'name_it', 'title_en', 'title_it'],
            'product_customization_print_techniques' => ['name_en', 'name_it'],
            'product_customization_rows' => ['position_name_en', 'position_name_it'],
        ] as $table => $columns) {
            $this->dropColumns($table, $columns);
        }
    }

    private function addPair(string $table, string $after, string $prefix): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $after)) {
            return;
        }

        $en = $prefix.'_en';
        $it = $prefix.'_it';

        Schema::table($table, function (Blueprint $blueprint) use ($table, $after, $en): void {
            if (! Schema::hasColumn($table, $en)) {
                $blueprint->string($en)->nullable()->after($after);
            }
        });

        Schema::table($table, function (Blueprint $blueprint) use ($table, $en, $it, $after): void {
            if (! Schema::hasColumn($table, $it)) {
                $afterIt = Schema::hasColumn($table, $en) ? $en : $after;
                $blueprint->string($it)->nullable()->after($afterIt);
            }
        });
    }

    /** @param  array<int, string>  $columns */
    private function dropColumns(string $table, array $columns): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $existing = array_values(array_filter($columns, fn (string $col): bool => Schema::hasColumn($table, $col)));
        if ($existing === []) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($existing): void {
            $blueprint->dropColumn($existing);
        });
    }
};
