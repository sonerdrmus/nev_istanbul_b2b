<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Mağaza ürün sayfasındaki varyasyon → özelleştirme → beden adım sırasını üretir.
 */
final class ProductVariationFlowSteps
{
    /** `depends_on` alanında ürün özelleştirme adımına bağlılık için ayrılmış değer. */
    public const CUSTOMIZATION_DEPENDS_ON = '__product_customization__';

    public static function customizationDependsOnLabel(): string
    {
        return 'Ürün özelleştirme';
    }

    public static function isCustomizationDependency(?string $dependsOn): bool
    {
        return trim((string) $dependsOn) === self::CUSTOMIZATION_DEPENDS_ON;
    }

    /**
     * @param  Collection<int, \App\Models\ProductVariation>  $orderedVariations  Bağımlılık sırasına göre dizilmiş varyasyonlar
     * @return array{
     *     steps: list<array{type: string, variation?: \App\Models\ProductVariation}>,
     *     customization_step_index: int,
     *     size_step_index: int,
     *     show_customization: bool,
     * }
     */
    public static function build(Product $product, Collection $orderedVariations): array
    {
        $afterCustomizationNames = [];
        foreach ($orderedVariations as $variation) {
            if (self::isCustomizationDependency($variation->depends_on)) {
                $afterCustomizationNames[(string) $variation->name] = true;
            }
        }

        $changed = true;
        while ($changed) {
            $changed = false;
            foreach ($orderedVariations as $variation) {
                $name = (string) $variation->name;
                if (isset($afterCustomizationNames[$name])) {
                    continue;
                }
                $dependsOn = trim((string) ($variation->depends_on ?? ''));
                if ($dependsOn !== '' && ! self::isCustomizationDependency($dependsOn) && isset($afterCustomizationNames[$dependsOn])) {
                    $afterCustomizationNames[$name] = true;
                    $changed = true;
                }
            }
        }

        $beforeCustomization = collect();
        $afterCustomization = collect();

        foreach ($orderedVariations as $variation) {
            if (isset($afterCustomizationNames[(string) $variation->name])) {
                $afterCustomization->push($variation);
            } else {
                $beforeCustomization->push($variation);
            }
        }

        $steps = $beforeCustomization
            ->map(fn ($variation) => ['type' => 'variation', 'variation' => $variation])
            ->values()
            ->all();

        $showCustomization = (bool) ($product->customization_enabled ?? true);
        $customizationStepIndex = -1;

        if ($showCustomization) {
            $trigger = trim((string) ($product->customization_trigger_variation ?? ''));
            $insertAt = count($steps);
            if ($trigger !== '') {
                foreach ($steps as $index => $step) {
                    if ($step['type'] !== 'variation') {
                        continue;
                    }
                    if (strcasecmp(trim((string) $step['variation']->name), $trigger) === 0) {
                        $insertAt = $index + 1;
                        break;
                    }
                }
            }
            array_splice($steps, $insertAt, 0, [['type' => 'customization']]);
        }

        foreach ($afterCustomization as $variation) {
            $steps[] = ['type' => 'variation', 'variation' => $variation];
        }

        $sizeStepIndex = -1;
        foreach ($steps as $index => $step) {
            if ($step['type'] === 'customization') {
                $customizationStepIndex = $index;
            }
            if ($step['type'] === 'size') {
                $sizeStepIndex = $index;
            }
        }

        return [
            'steps' => $steps,
            'customization_step_index' => $customizationStepIndex,
            'size_step_index' => $sizeStepIndex,
            'show_customization' => $showCustomization,
        ];
    }
}
