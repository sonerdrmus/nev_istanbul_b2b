<?php

namespace Tests\Feature;

use App\Models\ColorDimensionMultiplier;
use App\Models\Product;
use App\Models\ProductCustomizationRow;
use App\Models\SizeDimensionMultiplier;
use App\Support\DimensionMultiplierCatalog;
use App\Support\ProductCustomizationCatalog;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductCustomizationProductScopeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('product_customization_row_product');
        Schema::dropIfExists('product_customization_rows');
        Schema::dropIfExists('product_customization_print_techniques');
        Schema::dropIfExists('product_customization_settings');
        Schema::dropIfExists('size_dimension_multipliers');
        Schema::dropIfExists('color_dimension_multipliers');
        Schema::dropIfExists('quantity_dimension_multipliers');
        Schema::dropIfExists('products');

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->boolean('customization_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('product_customization_settings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedTinyInteger('max_color_count')->default(7);
            $table->string('default_print_technique_slug')->nullable();
            $table->timestamps();
        });

        Schema::create('product_customization_print_techniques', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_customization_rows', function (Blueprint $table): void {
            $table->id();
            $table->string('position_name');
            $table->string('position_image')->nullable();
            $table->decimal('default_width', 8, 2)->nullable();
            $table->decimal('default_height', 8, 2)->nullable();
            $table->unsignedTinyInteger('default_color_count')->default(3);
            $table->string('default_print_technique_slug')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_customization_row_product', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_customization_row_id')->constrained('product_customization_rows')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['product_customization_row_id', 'product_id'], 'pcr_product_unique');
        });

        Schema::create('size_dimension_multipliers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            $table->string('print_technique_slug')->default('emprime');
            $table->string('size_label');
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->decimal('auto_multiplier', 12, 2)->default(1);
            $table->string('fixed_multiplier')->nullable();
            $table->decimal('extra_multiplier', 12, 4)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('color_dimension_multipliers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            $table->string('print_technique_slug')->default('emprime');
            $table->unsignedTinyInteger('color_count');
            $table->decimal('multiplier_price', 12, 4)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('quantity_dimension_multipliers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            $table->string('print_technique_slug')->default('emprime');
            $table->unsignedInteger('quantity_from');
            $table->unsignedInteger('quantity_to');
            $table->decimal('multiplier_price', 12, 4)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        \App\Models\ProductCustomizationSetting::query()->create([
            'max_color_count' => 7,
            'default_print_technique_slug' => 'emprime',
        ]);

        \App\Models\ProductCustomizationPrintTechnique::query()->create([
            'name' => 'Emprime',
            'slug' => 'emprime',
            'sort_order' => 0,
            'is_active' => true,
        ]);
    }

    public function test_customization_row_only_visible_for_assigned_product(): void
    {
        $productA = Product::withoutEvents(fn () => Product::create(['name' => 'A', 'customization_enabled' => true]));
        $productB = Product::withoutEvents(fn () => Product::create(['name' => 'B', 'customization_enabled' => true]));

        $row = ProductCustomizationRow::query()->create([
            'position_name' => 'Ön',
            'is_active' => true,
            'sort_order' => 0,
        ]);
        $row->products()->sync([$productA->id]);

        $productA->load(['customizationRows' => fn ($q) => $q->where('is_active', true)]);
        $productB->load(['customizationRows' => fn ($q) => $q->where('is_active', true)]);

        $catalogA = ProductCustomizationCatalog::forStore($productA);
        $catalogB = ProductCustomizationCatalog::forStore($productB);

        $this->assertCount(1, $catalogA['rows']);
        $this->assertSame('Ön', $catalogA['rows']->first()->position_name);
        $this->assertCount(0, $catalogB['rows']);
    }

    public function test_product_multipliers_override_template(): void
    {
        $product = Product::withoutEvents(fn () => Product::create(['name' => 'P', 'customization_enabled' => true]));

        SizeDimensionMultiplier::query()->create([
            'product_id' => null,
            'print_technique_slug' => 'emprime',
            'size_label' => 'Template',
            'auto_multiplier' => 10,
            'extra_multiplier' => 1,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        SizeDimensionMultiplier::query()->create([
            'product_id' => $product->id,
            'print_technique_slug' => 'emprime',
            'size_label' => 'ProductOnly',
            'auto_multiplier' => 20,
            'extra_multiplier' => 2,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $grouped = DimensionMultiplierCatalog::groupedForStore($product);
        $labels = collect($grouped['emprime']['size'] ?? [])->pluck('size_label')->all();

        $this->assertSame(['ProductOnly'], $labels);
    }

    public function test_template_used_when_product_has_no_multipliers(): void
    {
        $product = Product::withoutEvents(fn () => Product::create(['name' => 'P', 'customization_enabled' => true]));

        SizeDimensionMultiplier::query()->create([
            'product_id' => null,
            'print_technique_slug' => 'emprime',
            'size_label' => 'Template',
            'auto_multiplier' => 10,
            'extra_multiplier' => 1,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        ColorDimensionMultiplier::query()->create([
            'product_id' => null,
            'print_technique_slug' => 'emprime',
            'color_count' => 2,
            'multiplier_price' => 1.5,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $grouped = DimensionMultiplierCatalog::groupedForStore($product);
        $this->assertSame(['Template'], collect($grouped['emprime']['size'] ?? [])->pluck('size_label')->all());
        $this->assertSame([2], collect($grouped['emprime']['color'] ?? [])->pluck('color_count')->all());
    }
}
