<?php

namespace App\Models\Concerns;

use App\Support\ProductVariationOptionInterfaceSync;

trait SyncsLinkedProductVariationOptions
{
    abstract protected static function linkedProductVariationType(): string;

    protected static function bootSyncsLinkedProductVariationOptions(): void
    {
        static::saved(function ($preset): void {
            ProductVariationOptionInterfaceSync::syncPreset($preset, static::linkedProductVariationType());
        });

        static::created(function ($preset): void {
            $type = static::linkedProductVariationType();

            if (ProductVariationOptionInterfaceSync::presetIsEligibleForProductOptions($type, $preset)) {
                ProductVariationOptionInterfaceSync::appendMissingProductOptions($type, $preset->getKey());
            }
        });

        static::deleting(function ($preset): void {
            $preset->productVariationOptions()->delete();
        });
    }
}
