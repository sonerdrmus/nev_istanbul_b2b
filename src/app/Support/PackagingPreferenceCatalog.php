<?php

namespace App\Support;

use App\Models\InterfacePackagingCustomization;
use App\Models\InterfacePackagingMaterial;
use App\Models\InterfacePackagingPreferenceVariation;
use App\Models\InterfacePackagingSetting;
use Illuminate\Support\Facades\Schema;

final class PackagingPreferenceCatalog
{
    /**
     * @return array{
     *     types: array<int, array<string, mixed>>,
     *     materials: array<int, array<string, mixed>>,
     *     customizations: array<int, array<string, mixed>>,
     *     customizations_enabled: bool,
     *     barcode: array<string, mixed>
     * }
     */
    public static function forStore(): array
    {
        if (! Schema::hasTable('interface_packaging_preference_variations')) {
            return self::emptyCatalog();
        }

        $types = InterfacePackagingPreferenceVariation::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (InterfacePackagingPreferenceVariation $t): array => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
                'requires_material' => (bool) $t->requires_material,
                'image_url' => filled($t->image_path) ? MediaUrl::public($t->image_path) : null,
            ])
            ->values()
            ->all();

        $materials = Schema::hasTable('interface_packaging_materials')
            ? InterfacePackagingMaterial::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (InterfacePackagingMaterial $m): array => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'slug' => $m->slug,
                ])
                ->values()
                ->all()
            : [];

        $settings = Schema::hasTable('interface_packaging_settings')
            ? InterfacePackagingSetting::instance()
            : null;

        $customizationsEnabled = (bool) ($settings?->customizations_enabled ?? true);

        $customizations = ($customizationsEnabled && Schema::hasTable('interface_packaging_customizations'))
            ? InterfacePackagingCustomization::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (InterfacePackagingCustomization $c): array => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'slug' => $c->slug,
                    'extra_price' => (float) $c->extra_price,
                    'is_default' => (bool) $c->is_default,
                ])
                ->values()
                ->all()
            : [];

        return [
            'types' => $types,
            'materials' => $materials,
            'customizations' => $customizations,
            'customizations_enabled' => $customizationsEnabled,
            'barcode' => [
                'enabled' => (bool) ($settings?->barcode_enabled ?? false),
                'label' => (string) ($settings?->barcode_label ?? ''),
                'extra_price' => (float) ($settings?->barcode_extra_price ?? 0),
                'description' => (string) ($settings?->barcode_description ?? ''),
                'image_url' => filled($settings?->barcode_image_path)
                    ? MediaUrl::public($settings->barcode_image_path)
                    : null,
            ],
        ];
    }

    /** @return array<string, mixed> */
    private static function emptyCatalog(): array
    {
        return [
            'types' => [],
            'materials' => [],
            'customizations' => [],
            'customizations_enabled' => false,
            'barcode' => [
                'enabled' => false,
                'label' => '',
                'extra_price' => 0,
                'description' => '',
                'image_url' => null,
            ],
        ];
    }
}
