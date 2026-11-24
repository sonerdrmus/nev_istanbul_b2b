<?php

namespace Tests\Feature;

use App\Filament\Resources\ProductResource;
use App\Models\InterfaceProductModelVariation;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Support\ProductVariationOptionInterfaceSync;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductModelAssignmentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('product_variation_options');
        Schema::dropIfExists('product_variations');
        Schema::dropIfExists('interface_product_model_variation_product');
        Schema::dropIfExists('interface_product_model_variations');
        Schema::dropIfExists('products');

        Schema::create('interface_product_model_variations', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->decimal('price_multiplier', 8, 3)->default(1);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('interface_product_model_variation_product', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('interface_product_model_variation_id')->constrained('interface_product_model_variations')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['interface_product_model_variation_id', 'product_id'], 'product_model_product_unique');
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
            $table->unsignedBigInteger('interface_product_model_variation_id')->nullable();
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

    private function makeModelVariation(Product $product): ProductVariation
    {
        return ProductVariation::withoutEvents(fn () => ProductVariation::create([
            'product_id' => $product->getKey(),
            'name' => 'Model Seçiniz',
            'type' => 'model_type',
        ]));
    }

    /** @return array<int, int> */
    private function modelOptionPresetIds(ProductVariation $variation): array
    {
        return $variation->options()
            ->orderBy('sort_order')
            ->pluck('interface_product_model_variation_id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    private function makeModel(string $name): InterfaceProductModelVariation
    {
        return InterfaceProductModelVariation::withoutEvents(
            fn () => InterfaceProductModelVariation::create(['name' => $name])
        );
    }

    public function test_model_can_be_assigned_to_multiple_products(): void
    {
        $model = $this->makeModel('Oversize');
        $productA = Product::withoutEvents(fn () => Product::create(['name' => 'T-Shirt A']));
        $productB = Product::withoutEvents(fn () => Product::create(['name' => 'T-Shirt B']));

        $model->products()->sync([$productA->id, $productB->id]);

        $this->assertCount(2, $model->fresh()->products);
    }

    public function test_unassigned_model_is_hidden_on_every_product(): void
    {
        $unassigned = $this->makeModel('Atanmamış Model');
        $product = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün']));

        $this->assertSame([$unassigned->id], $product->hiddenProductModelVariationIds());
        $this->assertFalse($unassigned->isVisibleForProduct((int) $product->id));
    }

    public function test_model_assigned_to_other_product_is_hidden(): void
    {
        $assigned = $this->makeModel('Sadece A Modeli');
        $unassigned = $this->makeModel('Atanmamış');

        $productA = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün A']));
        $productB = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün B']));

        $assigned->products()->sync([$productA->id]);

        $this->assertSame([$unassigned->id], $productA->hiddenProductModelVariationIds());
        $this->assertEqualsCanonicalizing([$assigned->id, $unassigned->id], $productB->hiddenProductModelVariationIds());
    }

    public function test_form_imports_only_models_assigned_to_the_product(): void
    {
        $onlyA = $this->makeModel('Sadece A');
        $unassigned = $this->makeModel('Atanmamış');

        $productA = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün A']));
        $productB = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün B']));
        $onlyA->products()->sync([$productA->id]);

        $presetIdsFor = fn (?int $productId): array => array_map(
            fn (array $row): int => (int) $row['interface_product_model_variation_id'],
            ProductResource::modelVariationOptionsFromInterfacePresets($productId),
        );

        $this->assertSame([$onlyA->id], $presetIdsFor($productA->id));
        $this->assertSame([], $presetIdsFor($productB->id));
        $this->assertSame([], $presetIdsFor(null));
        $this->assertNotContains($unassigned->id, $presetIdsFor($productA->id));
    }

    public function test_reconcile_keeps_product_model_options_up_to_date(): void
    {
        $unassigned = $this->makeModel('Atanmamış');
        $productA = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün A']));
        $productB = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün B']));

        $variationA = $this->makeModelVariation($productA);
        $variationB = $this->makeModelVariation($productB);

        ProductVariationOptionInterfaceSync::reconcileProductModelProductOptions();
        $this->assertSame([], $this->modelOptionPresetIds($variationA));
        $this->assertSame([], $this->modelOptionPresetIds($variationB));
        $this->assertNotContains($unassigned->id, $this->modelOptionPresetIds($variationA));

        $onlyA = $this->makeModel('Sadece A');
        $onlyA->products()->sync([$productA->id]);
        ProductVariationOptionInterfaceSync::reconcileProductModelProductOptions();

        $this->assertSame([$onlyA->id], $this->modelOptionPresetIds($variationA));
        $this->assertSame([], $this->modelOptionPresetIds($variationB));

        $onlyA->products()->sync([$productB->id]);
        ProductVariationOptionInterfaceSync::reconcileProductModelProductOptions();

        $this->assertSame([], $this->modelOptionPresetIds($variationA));
        $this->assertSame([$onlyA->id], $this->modelOptionPresetIds($variationB));
    }
}
