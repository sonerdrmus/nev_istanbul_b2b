<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addTextPair('interface_fabric_type_variations', 'detail_text', 'detail_text');
        $this->addTextPair('interface_certificate_variations', 'description', 'description');
        $this->addTextPair('interface_delivery_method_variations', 'description', 'description');
        $this->addTextPair('interface_delivery_method_sub_options', 'description', 'description');
        $this->addStringPair('interface_delivery_method_variations', 'estimated_delivery_time', 'estimated_delivery_time');

        if (Schema::hasTable('interface_delivery_method_variations')
            && Schema::hasColumn('interface_delivery_method_variations', 'estimated_delivery_time_en')) {
            DB::table('interface_delivery_method_variations')
                ->where('estimated_delivery_time', 'En geç 20 günde teslim')
                ->where(function ($query): void {
                    $query->whereNull('estimated_delivery_time_en')->orWhere('estimated_delivery_time_en', '');
                })
                ->update([
                    'estimated_delivery_time_en' => 'Delivery within 20 days',
                    'estimated_delivery_time_it' => 'Consegna entro 20 giorni',
                ]);

            DB::table('interface_delivery_method_variations')
                ->where('estimated_delivery_time', 'En geç 5 gün içerisinde teslim edilecektir.')
                ->where(function ($query): void {
                    $query->whereNull('estimated_delivery_time_en')->orWhere('estimated_delivery_time_en', '');
                })
                ->update([
                    'estimated_delivery_time_en' => 'Delivery within 5 days',
                    'estimated_delivery_time_it' => 'Consegna entro 5 giorni',
                ]);
        }

        if (Schema::hasTable('product_customization_print_techniques')) {
            DB::table('product_customization_print_techniques')
                ->where('name_en', 'Screen printingı')
                ->update(['name_en' => 'Screen printing']);
            DB::table('product_customization_print_techniques')
                ->where('name_it', 'Screen printingı')
                ->update(['name_it' => 'Stampa serigrafica']);
        }
    }

    public function down(): void
    {
        $this->dropColumns('interface_fabric_type_variations', ['detail_text_en', 'detail_text_it']);
        $this->dropColumns('interface_certificate_variations', ['description_en', 'description_it']);
        $this->dropColumns('interface_delivery_method_variations', [
            'description_en',
            'description_it',
            'estimated_delivery_time_en',
            'estimated_delivery_time_it',
        ]);
        $this->dropColumns('interface_delivery_method_sub_options', ['description_en', 'description_it']);
    }

    private function addTextPair(string $table, string $after, string $prefix): void
    {
        $this->addPair($table, $after, $prefix, 'text');
    }

    private function addStringPair(string $table, string $after, string $prefix): void
    {
        $this->addPair($table, $after, $prefix, 'string');
    }

    private function addPair(string $table, string $after, string $prefix, string $type): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $after)) {
            return;
        }

        $en = $prefix.'_en';
        $it = $prefix.'_it';

        Schema::table($table, function (Blueprint $blueprint) use ($table, $after, $en, $type): void {
            if (! Schema::hasColumn($table, $en)) {
                $column = $type === 'text' ? $blueprint->text($en) : $blueprint->string($en);
                $column->nullable()->after($after);
            }
        });

        Schema::table($table, function (Blueprint $blueprint) use ($table, $en, $it, $after, $type): void {
            if (! Schema::hasColumn($table, $it)) {
                $afterIt = Schema::hasColumn($table, $en) ? $en : $after;
                $column = $type === 'text' ? $blueprint->text($it) : $blueprint->string($it);
                $column->nullable()->after($afterIt);
            }
        });
    }

    /** @param  array<int, string>  $columns */
    private function dropColumns(string $table, array $columns): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $existing = array_values(array_filter($columns, fn (string $column): bool => Schema::hasColumn($table, $column)));
        if ($existing === []) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($existing): void {
            $blueprint->dropColumn($existing);
        });
    }
};
