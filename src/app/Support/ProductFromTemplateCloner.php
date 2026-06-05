<?php

namespace App\Support;

use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Mevcut bir ürünün temel alanlarını ve varyasyon ağacını yeni ürüne kopyalar.
 */
final class ProductFromTemplateCloner
{
    /**
     * @param  array<string, mixed>  $overrides  name (zorunlu), slug, category_id, description, sort_order, price, ...
     */
    public function clone(Product $template, array $overrides): Product
    {
        $name = trim((string) ($overrides['name'] ?? ''));
        if ($name === '') {
            throw new RuntimeException('Klonlanacak ürün adı gerekli.');
        }

        $template->loadMissing('variations.options');

        $slug = trim((string) ($overrides['slug'] ?? Str::slug($name)));
        if ($slug === '') {
            $slug = Str::slug($name);
        }

        $attributes = [
            'company_id' => $overrides['company_id'] ?? $template->company_id,
            'category_id' => $overrides['category_id'] ?? $template->category_id,
            'tax_class_id' => $overrides['tax_class_id'] ?? $template->tax_class_id,
            'currency_id' => $overrides['currency_id'] ?? $template->currency_id,
            'name' => $name,
            'slug' => $slug,
            'description' => $overrides['description'] ?? $template->description,
            'meta_title' => $overrides['meta_title'] ?? null,
            'meta_description' => $overrides['meta_description'] ?? null,
            'meta_keywords' => $overrides['meta_keywords'] ?? null,
            'price' => $overrides['price'] ?? $template->price,
            'stock_quantity' => $overrides['stock_quantity'] ?? $template->stock_quantity,
            'minimum_order_quantity' => $overrides['minimum_order_quantity'] ?? $template->minimum_order_quantity,
            'image' => $overrides['image'] ?? $template->image,
            'is_active' => $overrides['is_active'] ?? $template->is_active,
            'status' => $overrides['status'] ?? $template->status,
            'sort_order' => $overrides['sort_order'] ?? $template->sort_order,
            'show_on_home' => $overrides['show_on_home'] ?? false,
            'home_showcase_order' => $overrides['home_showcase_order'] ?? 0,
            'home_showcase_image' => $overrides['home_showcase_image'] ?? null,
            'size_table_trigger_variation' => $overrides['size_table_trigger_variation'] ?? $template->size_table_trigger_variation,
            'customization_enabled' => $overrides['customization_enabled'] ?? $template->customization_enabled,
            'customization_trigger_variation' => $overrides['customization_trigger_variation'] ?? $template->customization_trigger_variation,
        ];

        $product = Product::query()->create($attributes);

        /** @var array<int, int> $variationIdMap */
        $variationIdMap = [];
        foreach ($template->variations as $variation) {
            $newVariation = ProductVariation::query()->create([
                'product_id' => $product->getKey(),
                'name' => $variation->name,
                'type' => $variation->type,
                'depends_on' => $variation->depends_on,
                'sort_order' => $variation->sort_order,
                'replace_main_gallery_image' => $variation->replace_main_gallery_image,
                'allows_multiple' => $variation->allows_multiple,
                'solo_option_value' => $variation->solo_option_value,
                'info_text' => $variation->info_text,
            ]);
            $variationIdMap[(int) $variation->getKey()] = (int) $newVariation->getKey();
        }

        /** @var array<int, int> $optionIdMap */
        $optionIdMap = [];

        foreach ($this->orderVariationsByDependency($template->variations) as $variation) {
            foreach ($variation->options as $option) {
                $parentOptionIds = $this->mapParentOptionIds($option->parent_option_ids, $optionIdMap);
                $parentOptionId = $option->parent_option_id !== null
                    ? ($optionIdMap[(int) $option->parent_option_id] ?? null)
                    : null;

                $newOption = ProductVariationOption::query()->create([
                    'product_variation_id' => $variationIdMap[(int) $variation->getKey()],
                    'interface_color_variation_id' => $option->interface_color_variation_id,
                    'interface_fabric_type_variation_id' => $option->interface_fabric_type_variation_id,
                    'interface_label_type_variation_id' => $option->interface_label_type_variation_id,
                    'interface_packaging_preference_variation_id' => $option->interface_packaging_preference_variation_id,
                    'interface_certificate_variation_id' => $option->interface_certificate_variation_id,
                    'interface_delivery_method_variation_id' => $option->interface_delivery_method_variation_id,
                    'size_table_id' => $option->size_table_id,
                    'option_value' => $option->option_value,
                    'info_text' => $option->info_text,
                    'option_color' => $option->option_color,
                    'option_image' => $option->option_image,
                    'option_image_size' => $option->option_image_size,
                    'price_delta' => $option->price_delta,
                    'stock_quantity' => $option->stock_quantity,
                    'parent_option_id' => $parentOptionId,
                    'parent_option_ids' => $parentOptionIds,
                    'sort_order' => $option->sort_order,
                ]);

                $optionIdMap[(int) $option->getKey()] = (int) $newOption->getKey();
            }
        }

        foreach ($template->variations as $variation) {
            $mappedDependsOnOptionIds = $this->mapParentOptionIds($variation->depends_on_option_ids, $optionIdMap);
            if ($mappedDependsOnOptionIds === null) {
                continue;
            }

            ProductVariation::query()
                ->whereKey($variationIdMap[(int) $variation->getKey()])
                ->update(['depends_on_option_ids' => $mappedDependsOnOptionIds]);
        }

        return $product->fresh(['variations.options']);
    }

    /**
     * @param  EloquentCollection<int, ProductVariation>  $variations
     * @return list<ProductVariation>
     */
    private function orderVariationsByDependency(EloquentCollection $variations): array
    {
        $byName = $variations->keyBy('name');
        $ordered = [];
        $visited = [];

        $visit = function (ProductVariation $variation) use (&$visit, &$ordered, &$visited, $byName): void {
            $key = (string) $variation->name;
            if (isset($visited[$key])) {
                return;
            }
            $visited[$key] = true;

            $dependsOn = trim((string) ($variation->depends_on ?? ''));
            if ($dependsOn !== '' && $byName->has($dependsOn)) {
                $visit($byName->get($dependsOn));
            }

            $ordered[] = $variation;
        };

        foreach ($variations as $variation) {
            $visit($variation);
        }

        return $ordered;
    }

    /**
     * @param  array<int|string, int|string>|null  $ids
     * @param  array<int, int>  $optionIdMap
     * @return array<int, int>|null
     */
    private function mapParentOptionIds(?array $ids, array $optionIdMap): ?array
    {
        if ($ids === null || $ids === []) {
            return $ids;
        }

        $mapped = [];
        foreach ($ids as $id) {
            $oldId = (int) $id;
            if (isset($optionIdMap[$oldId])) {
                $mapped[] = $optionIdMap[$oldId];
            }
        }

        return $mapped === [] ? null : array_values($mapped);
    }
}
