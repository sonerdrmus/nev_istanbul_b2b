<?php

namespace Tests\Feature;

use App\Support\ProductCustomizationPrintTotal;
use Tests\TestCase;

class ProductCustomizationPrintTotalTest extends TestCase
{
    public function test_sums_row_totals_from_variation_data(): void
    {
        $total = ProductCustomizationPrintTotal::tryFromVariationData([
            'product_customization' => 'completed',
            'product_customization_table' => [
                'rows' => [
                    ['total_price_try' => 100.5],
                    ['total_price_try' => 49.5],
                ],
            ],
        ]);

        $this->assertEqualsWithDelta(150.0, $total, 0.001);
    }

    public function test_prefers_print_total_try_field(): void
    {
        $total = ProductCustomizationPrintTotal::tryFromVariationData([
            'product_customization' => 'completed',
            'product_customization_table' => [
                'print_total_try' => 77.25,
                'rows' => [
                    ['total_price_try' => 10],
                ],
            ],
        ]);

        $this->assertEqualsWithDelta(77.25, $total, 0.001);
    }

    public function test_skipped_customization_is_zero(): void
    {
        $total = ProductCustomizationPrintTotal::tryFromVariationData([
            'product_customization' => 'skipped',
            'product_customization_table' => [
                'print_total_try' => 100,
            ],
        ]);

        $this->assertSame(0.0, $total);
    }

    public function test_line_total_multiplies_by_order_quantity(): void
    {
        $variation = [
            'product_customization' => 'completed',
            'product_customization_table' => [
                'print_total_try' => 72.60,
            ],
        ];

        $this->assertEqualsWithDelta(72.60, ProductCustomizationPrintTotal::tryFromVariationData($variation), 0.001);
        $this->assertEqualsWithDelta(726.0, ProductCustomizationPrintTotal::lineTotalTryFromVariationData($variation, 10), 0.001);
        $this->assertSame(0.0, ProductCustomizationPrintTotal::lineTotalTryFromVariationData($variation, 0));
    }
}
