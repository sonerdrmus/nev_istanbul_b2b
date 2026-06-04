<?php

use App\Models\ColorDimensionMultiplier;
use App\Models\QuantityDimensionMultiplier;
use App\Models\SizeDimensionMultiplier;
use App\Support\PrintTechniqueDimensionMultiplierTypes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addPrintTechniqueColumn('size_dimension_multipliers');
        $this->addPrintTechniqueColumn('color_dimension_multipliers', true);
        $this->addPrintTechniqueColumn('quantity_dimension_multipliers', true);

        $this->migrateExistingRows();
    }

    public function down(): void
    {
        foreach (['size_dimension_multipliers', 'color_dimension_multipliers', 'quantity_dimension_multipliers'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'print_technique_slug')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                if ($table === 'color_dimension_multipliers') {
                    $blueprint->dropUnique('color_dim_mult_print_color_unique');
                }

                if ($table === 'quantity_dimension_multipliers') {
                    $blueprint->dropUnique('qty_dim_mult_print_range_unique');
                }

                if ($table !== 'size_dimension_multipliers') {
                    $this->restoreLegacyUniqueIndexes($blueprint, $table);
                }

                $blueprint->dropIndex($table.'_print_active_sort_idx');
                $blueprint->dropColumn('print_technique_slug');
            });
        }
    }

    private function addPrintTechniqueColumn(string $table, bool $hadUniqueConstraints = false): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        if (Schema::hasColumn($table, 'print_technique_slug')) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($table, $hadUniqueConstraints) {
            if ($hadUniqueConstraints) {
                $this->dropLegacyUniqueIndexes($blueprint, $table);
            }

            $blueprint->string('print_technique_slug', 64)
                ->default(PrintTechniqueDimensionMultiplierTypes::SLUG_EMPRIME)
                ->after('id');
            $blueprint->index(['print_technique_slug', 'is_active', 'sort_order'], $table.'_print_active_sort_idx');
        });
    }

    private function dropLegacyUniqueIndexes(Blueprint $table, string $tableName): void
    {
        if ($tableName === 'color_dimension_multipliers') {
            $table->dropUnique('color_dimension_multipliers_color_count_unique');
        }

        if ($tableName === 'quantity_dimension_multipliers') {
            $table->dropUnique('quantity_dimension_multipliers_quantity_from_quantity_to_unique');
        }
    }

    private function restoreLegacyUniqueIndexes(Blueprint $table, string $tableName): void
    {
        if ($tableName === 'color_dimension_multipliers') {
            $table->unique('color_count');
        }

        if ($tableName === 'quantity_dimension_multipliers') {
            $table->unique(['quantity_from', 'quantity_to']);
        }
    }

    private function migrateExistingRows(): void
    {
        $defaultSlug = PrintTechniqueDimensionMultiplierTypes::SLUG_EMPRIME;
        $otherSlugs = array_values(array_filter(
            PrintTechniqueDimensionMultiplierTypes::slugs(),
            fn (string $slug): bool => $slug !== $defaultSlug,
        ));

        $this->assignDefaultSlug(SizeDimensionMultiplier::class, $defaultSlug);
        $this->assignDefaultSlug(ColorDimensionMultiplier::class, $defaultSlug);
        $this->assignDefaultSlug(QuantityDimensionMultiplier::class, $defaultSlug);

        $this->duplicateRowsForOtherPrintTypes(SizeDimensionMultiplier::class, $defaultSlug, $otherSlugs);
        $this->duplicateRowsForOtherPrintTypes(QuantityDimensionMultiplier::class, $defaultSlug, $otherSlugs);

        if (Schema::hasTable('color_dimension_multipliers')) {
            Schema::table('color_dimension_multipliers', function (Blueprint $table) {
                $table->unique(
                    ['print_technique_slug', 'color_count'],
                    'color_dim_mult_print_color_unique',
                );
            });
        }

        if (Schema::hasTable('quantity_dimension_multipliers')) {
            Schema::table('quantity_dimension_multipliers', function (Blueprint $table) {
                $table->unique(
                    ['print_technique_slug', 'quantity_from', 'quantity_to'],
                    'qty_dim_mult_print_range_unique',
                );
            });
        }
    }

    /**
     * @param  class-string  $modelClass
     */
    private function assignDefaultSlug(string $modelClass, string $defaultSlug): void
    {
        if (! Schema::hasTable((new $modelClass)->getTable())) {
            return;
        }

        $modelClass::query()
            ->where(function ($query) {
                $query->whereNull('print_technique_slug')
                    ->orWhere('print_technique_slug', '');
            })
            ->update(['print_technique_slug' => $defaultSlug]);
    }

    /**
     * @param  class-string  $modelClass
     * @param  list<string>  $otherSlugs
     */
    private function duplicateRowsForOtherPrintTypes(string $modelClass, string $sourceSlug, array $otherSlugs): void
    {
        if ($otherSlugs === [] || ! Schema::hasTable((new $modelClass)->getTable())) {
            return;
        }

        $sourceRows = $modelClass::query()
            ->where('print_technique_slug', $sourceSlug)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        foreach ($otherSlugs as $targetSlug) {
            if ($modelClass::query()->where('print_technique_slug', $targetSlug)->exists()) {
                continue;
            }

            foreach ($sourceRows as $row) {
                $attrs = $row->only($row->getFillable());
                unset($attrs['id']);
                $attrs['print_technique_slug'] = $targetSlug;
                $modelClass::query()->create($attrs);
            }
        }
    }
};
