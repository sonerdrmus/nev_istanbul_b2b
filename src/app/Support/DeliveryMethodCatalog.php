<?php

namespace App\Support;

use App\Models\InterfaceDeliveryMethodSubOption;
use Illuminate\Support\Facades\Schema;

final class DeliveryMethodCatalog
{
    /**
     * @return array{
     *     sub_options: array<string, array<int, array<string, mixed>>>
     * }
     */
    public static function forStore(): array
    {
        if (! Schema::hasTable('interface_delivery_method_sub_options')) {
            return ['sub_options' => []];
        }

        $subOptions = [];

        InterfaceDeliveryMethodSubOption::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('interface_delivery_method_variation_id')
            ->each(function ($group, $presetId) use (&$subOptions): void {
                $subOptions[(string) $presetId] = $group
                    ->map(fn (InterfaceDeliveryMethodSubOption $option): array => [
                        'id' => $option->id,
                        'name' => $option->name,
                        'description' => (string) ($option->description ?? ''),
                        'price_multiplier' => (float) ($option->price_multiplier ?? 1),
                        'is_default' => (bool) $option->is_default,
                    ])
                    ->values()
                    ->all();
            });

        return ['sub_options' => $subOptions];
    }
}
