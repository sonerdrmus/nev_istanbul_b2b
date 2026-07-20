<?php

namespace Tests\Feature;

use App\Models\InterfaceColorVariation;
use App\Models\InterfaceFabricTypeVariation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InterfaceColorVariationFabricTypeGroupsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('interface_color_variation_interface_fabric_type_variation');
        Schema::dropIfExists('interface_color_variations');
        Schema::dropIfExists('interface_fabric_type_variations');

        Schema::create('interface_fabric_type_variations', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('interface_color_variations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('interface_fabric_type_variation_id')->nullable()->constrained('interface_fabric_type_variations')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('interface_color_variation_interface_fabric_type_variation', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('interface_color_variation_id')->constrained('interface_color_variations')->cascadeOnDelete();
            $table->foreignId('interface_fabric_type_variation_id')->constrained('interface_fabric_type_variations')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['interface_color_variation_id', 'interface_fabric_type_variation_id']);
        });
    }

    public function test_color_variation_can_be_linked_to_multiple_fabric_type_groups(): void
    {
        $groupA = InterfaceFabricTypeVariation::withoutEvents(fn () => InterfaceFabricTypeVariation::create(['name' => 'Compact Penye']));
        $groupB = InterfaceFabricTypeVariation::withoutEvents(fn () => InterfaceFabricTypeVariation::create(['name' => 'Polo']));

        $color = InterfaceColorVariation::withoutEvents(fn () => InterfaceColorVariation::create(['name' => 'Mavi', 'image_path' => 'colors/blue.png']));
        $color->fabricTypeVariations()->sync([$groupA->id, $groupB->id]);

        $linkedGroups = $color->fresh()->fabricTypeVariations()->pluck('name');

        $this->assertCount(2, $linkedGroups);
        $this->assertTrue($linkedGroups->contains('Compact Penye'));
        $this->assertTrue($linkedGroups->contains('Polo'));
    }

    public function test_fabric_type_can_link_multiple_colors_via_pivot(): void
    {
        $fabric = InterfaceFabricTypeVariation::withoutEvents(fn () => InterfaceFabricTypeVariation::create(['name' => 'Compact Penye']));
        $red = InterfaceColorVariation::withoutEvents(fn () => InterfaceColorVariation::create(['name' => 'Red', 'image_path' => 'colors/red.png']));
        $blue = InterfaceColorVariation::withoutEvents(fn () => InterfaceColorVariation::create(['name' => 'Blue', 'image_path' => 'colors/blue.png']));

        $fabric->colorVariations()->sync([$red->id, $blue->id]);

        $linked = $fabric->fresh()->colorVariations()->pluck('name');

        $this->assertCount(2, $linked);
        $this->assertTrue($linked->contains('Red'));
        $this->assertTrue($linked->contains('Blue'));
    }

    public function test_color_resolves_fabric_group_ids_from_pivot_and_legacy_fk(): void
    {
        $fabricA = InterfaceFabricTypeVariation::withoutEvents(fn () => InterfaceFabricTypeVariation::create(['name' => 'A']));
        $fabricB = InterfaceFabricTypeVariation::withoutEvents(fn () => InterfaceFabricTypeVariation::create(['name' => 'B']));

        $color = InterfaceColorVariation::withoutEvents(fn () => InterfaceColorVariation::create([
            'name' => 'Navy',
            'image_path' => 'colors/navy.png',
            'interface_fabric_type_variation_id' => $fabricA->id,
        ]));
        $color->fabricTypeVariations()->sync([$fabricB->id]);

        $ids = $color->fresh(['fabricTypeVariations'])->resolveFabricTypeVariationIdsForStore();

        $this->assertEqualsCanonicalizing([$fabricA->id, $fabricB->id], $ids);
    }
}
