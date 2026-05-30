<?php

use App\Models\QuantityDimensionMultiplier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('quantity_dimension_multipliers')) {
            return;
        }

        $rows = [
            ['quantity_from' => 1, 'quantity_to' => 50, 'sort_order' => 10],
            ['quantity_from' => 51, 'quantity_to' => 75, 'sort_order' => 20],
            ['quantity_from' => 76, 'quantity_to' => 125, 'sort_order' => 30],
            ['quantity_from' => 126, 'quantity_to' => 200, 'sort_order' => 40],
            ['quantity_from' => 201, 'quantity_to' => 275, 'sort_order' => 50],
            ['quantity_from' => 276, 'quantity_to' => 350, 'sort_order' => 60],
            ['quantity_from' => 351, 'quantity_to' => 500, 'sort_order' => 70],
            ['quantity_from' => 501, 'quantity_to' => 700, 'sort_order' => 80],
            ['quantity_from' => 701, 'quantity_to' => 1000, 'sort_order' => 90],
        ];

        foreach ($rows as $item) {
            QuantityDimensionMultiplier::query()->updateOrCreate(
                [
                    'quantity_from' => $item['quantity_from'],
                    'quantity_to' => $item['quantity_to'],
                ],
                [
                    'multiplier_price' => 0,
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                ],
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('quantity_dimension_multipliers')) {
            return;
        }

        $ranges = [
            [1, 50],
            [51, 75],
            [76, 125],
            [126, 200],
            [201, 275],
            [276, 350],
            [351, 500],
            [501, 700],
            [701, 1000],
        ];

        foreach ($ranges as [$from, $to]) {
            QuantityDimensionMultiplier::query()
                ->where('quantity_from', $from)
                ->where('quantity_to', $to)
                ->delete();
        }
    }
};
