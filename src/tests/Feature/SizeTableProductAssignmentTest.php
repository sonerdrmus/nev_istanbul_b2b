<?php

namespace Tests\Feature;

use App\Filament\Resources\ProductResource;
use App\Filament\Resources\SizeTableResource;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;
use App\Models\SizeTable;
use App\Support\ProductVariationOptionInterfaceSync;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SizeTableProductAssignmentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('product_variation_options');
        Schema::dropIfExists('product_variations');
        Schema::dropIfExists('size_table_product');
        Schema::dropIfExists('size_tables');
        Schema::dropIfExists('products');

        Schema::create('size_tables', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('title')->nullable();
            $table->string('trigger_variation_name')->nullable();
            $table->string('trigger_option_value')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('size_table_product', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('size_table_id')->constrained('size_tables')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['size_table_id', 'product_id'], 'size_table_product_unique');
        });

        Schema::create('product_variations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('type')->default('select');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_variation_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_variation_id')->constrained('product_variations')->cascadeOnDelete();
            $table->string('option_value')->nullable();
            $table->string('option_value_en')->nullable();
            $table->string('option_value_it')->nullable();
            $table->text('info_text')->nullable();
            $table->unsignedBigInteger('interface_color_variation_id')->nullable();
            $table->unsignedBigInteger('interface_fabric_type_variation_id')->nullable();
            $table->unsignedBigInteger('interface_label_type_variation_id')->nullable();
            $table->unsignedBigInteger('interface_certificate_variation_id')->nullable();
            $table->unsignedBigInteger('interface_mold_model_variation_id')->nullable();
            $table->unsignedBigInteger('interface_delivery_method_variation_id')->nullable();
            $table->unsignedBigInteger('interface_packaging_preference_variation_id')->nullable();
            $table->unsignedBigInteger('size_table_id')->nullable();
            $table->string('option_image')->nullable();
            $table->string('option_color')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->decimal('price_delta', 12, 4)->default(0);
            $table->unsignedInteger('stock_quantity')->nullable();
            $table->unsignedBigInteger('parent_option_id')->nullable();
            $table->text('parent_option_ids')->nullable();
            $table->timestamps();
        });
    }

    private function makeSizeTable(string $name): SizeTable
    {
        return SizeTable::withoutEvents(fn () => SizeTable::create([
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '-', $name)),
            'title' => $name,
        ]));
    }

    private function makeSizeVariation(Product $product): ProductVariation
    {
        return ProductVariation::withoutEvents(fn () => ProductVariation::create([
            'product_id' => $product->getKey(),
            'name' => 'Sipariş Adeti',
            'type' => 'size_table',
        ]));
    }

    /** @return array<int, int> */
    private function sizeOptionPresetIds(ProductVariation $variation): array
    {
        return $variation->options()
            ->orderBy('sort_order')
            ->pluck('size_table_id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    public function test_unassigned_size_table_is_global(): void
    {
        $global = $this->makeSizeTable('Global Tablo');
        $product = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün']));

        $this->assertSame([], $product->hiddenSizeTableIds());
        $this->assertTrue($global->isVisibleForProduct((int) $product->id));
    }

    public function test_assigned_size_table_is_hidden_on_other_products(): void
    {
        $assigned = $this->makeSizeTable('Sadece A');
        $productA = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün A']));
        $productB = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün B']));

        $assigned->products()->sync([$productA->id]);

        $this->assertSame([], $productA->hiddenSizeTableIds());
        $this->assertSame([$assigned->id], $productB->hiddenSizeTableIds());
        $this->assertTrue($assigned->isVisibleForProduct((int) $productA->id));
        $this->assertFalse($assigned->isVisibleForProduct((int) $productB->id));
    }

    public function test_form_imports_global_and_assigned_tables(): void
    {
        $global = $this->makeSizeTable('Global');
        $onlyA = $this->makeSizeTable('Sadece A');
        $productA = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün A']));
        $productB = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün B']));
        $onlyA->products()->sync([$productA->id]);

        $presetIdsFor = fn (?int $productId): array => array_map(
            fn (array $row): int => (int) $row['size_table_id'],
            ProductResource::sizeTableVariationOptionsFromPresets($productId),
        );

        $this->assertEqualsCanonicalizing([$global->id, $onlyA->id], $presetIdsFor($productA->id));
        $this->assertSame([$global->id], $presetIdsFor($productB->id));
        $this->assertSame([$global->id], $presetIdsFor(null));
    }

    public function test_reconcile_respects_product_assignment(): void
    {
        $global = $this->makeSizeTable('Global');
        $onlyA = $this->makeSizeTable('Sadece A');
        $productA = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün A']));
        $productB = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün B']));
        $onlyA->products()->sync([$productA->id]);

        $variationA = $this->makeSizeVariation($productA);
        $variationB = $this->makeSizeVariation($productB);

        ProductVariationOptionInterfaceSync::reconcileSizeTableProductOptions();

        $this->assertEqualsCanonicalizing([$global->id, $onlyA->id], $this->sizeOptionPresetIds($variationA));
        $this->assertSame([$global->id], $this->sizeOptionPresetIds($variationB));
    }

    public function test_trigger_options_scoped_to_selected_products(): void
    {
        $productA = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün A']));
        $productB = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün B']));

        $variationA = ProductVariation::withoutEvents(fn () => ProductVariation::create([
            'product_id' => $productA->id,
            'name' => 'Cinsiyet',
            'type' => 'select',
        ]));
        ProductVariationOption::withoutEvents(fn () => ProductVariationOption::create([
            'product_variation_id' => $variationA->id,
            'option_value' => 'Erkek',
        ]));

        ProductVariation::withoutEvents(fn () => ProductVariation::create([
            'product_id' => $productB->id,
            'name' => 'Yaş Grubu',
            'type' => 'select',
        ]));

        $optionsA = SizeTableResource::getTriggerVariationOptions([$productA->id]);
        $this->assertArrayHasKey('Cinsiyet', $optionsA);
        $this->assertArrayHasKey('Cinsiyet|Erkek', $optionsA);
        $this->assertArrayNotHasKey('Yaş Grubu', $optionsA);

        $optionsAll = SizeTableResource::getTriggerVariationOptions(null);
        $this->assertArrayHasKey('Cinsiyet', $optionsAll);
        $this->assertArrayHasKey('Yaş Grubu', $optionsAll);
    }
}
