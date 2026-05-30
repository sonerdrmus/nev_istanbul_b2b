<?php

use App\Models\SizeDimensionMultiplier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('size_dimension_multipliers')) {
            return;
        }

        $rows = [
            [
                'size_label' => 'MAX',
                'width' => 37.8,
                'height' => 53.46,
                'auto_multiplier' => 2021,
                'fixed_multiplier' => null,
                'extra_multiplier' => 2,
                'sort_order' => 10,
            ],
            [
                'size_label' => 'A3',
                'width' => 26.73,
                'height' => 37.8,
                'auto_multiplier' => 1010,
                'fixed_multiplier' => 'SABİT FİYAT',
                'extra_multiplier' => 1.34,
                'sort_order' => 20,
            ],
            [
                'size_label' => 'A4',
                'width' => 18.9,
                'height' => 26.73,
                'auto_multiplier' => 505,
                'fixed_multiplier' => '0.3',
                'extra_multiplier' => 1,
                'sort_order' => 30,
            ],
            [
                'size_label' => 'A5',
                'width' => 13.32,
                'height' => 18.9,
                'auto_multiplier' => 252,
                'fixed_multiplier' => '0.9',
                'extra_multiplier' => 0,
                'sort_order' => 40,
            ],
            [
                'size_label' => 'A6',
                'width' => 9.45,
                'height' => 13.32,
                'auto_multiplier' => 126,
                'fixed_multiplier' => '0.8',
                'extra_multiplier' => 0,
                'sort_order' => 50,
            ],
            [
                'size_label' => 'A7',
                'width' => 6.66,
                'height' => 9.45,
                'auto_multiplier' => 63,
                'fixed_multiplier' => '0.7',
                'extra_multiplier' => 0,
                'sort_order' => 60,
            ],
            [
                'size_label' => 'A8',
                'width' => 4.68,
                'height' => 6.66,
                'auto_multiplier' => 31,
                'fixed_multiplier' => '0.66',
                'extra_multiplier' => 0,
                'sort_order' => 70,
            ],
            [
                'size_label' => 'A9',
                'width' => 3.33,
                'height' => 4.68,
                'auto_multiplier' => 16,
                'fixed_multiplier' => '0.6',
                'extra_multiplier' => 0,
                'sort_order' => 80,
            ],
            [
                'size_label' => 'A10',
                'width' => 2.34,
                'height' => 3.33,
                'auto_multiplier' => 8,
                'fixed_multiplier' => '0.55',
                'extra_multiplier' => 0,
                'sort_order' => 90,
            ],
        ];

        foreach ($rows as $item) {
            SizeDimensionMultiplier::query()->updateOrCreate(
                ['size_label' => $item['size_label']],
                array_merge($item, ['is_active' => true]),
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('size_dimension_multipliers')) {
            return;
        }

        SizeDimensionMultiplier::query()
            ->whereIn('size_label', ['MAX', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'A10'])
            ->delete();
    }
};
