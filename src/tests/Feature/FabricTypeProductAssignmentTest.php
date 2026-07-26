<?php

namespace Tests\Feature;

use App\Filament\Resources\ProductResource;
use App\Models\InterfaceFabricTypeVariation;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Support\ProductVariationOptionInterfaceSync;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FabricTypeProductAssignmentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('product_variation_options');
        Schema::dropIfExists('product_variations');
        Schema::dropIfExists('interface_fabric_type_variation_product');
        Schema::dropIfExists('interface_fabric_type_variations');
        Schema::dropIfExists('products');

        Schema::create('interface_fabric_type_variations', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('interface_fabric_type_variation_product', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('interface_fabric_type_variation_id')->constrained('interface_fabric_type_variations')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['interface_fabric_type_variation_id', 'product_id'], 'fabric_type_product_unique');
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

    private function makeFabricVariation(Product $product): ProductVariation
    {
        return ProductVariation::withoutEvents(fn () => ProductVariation::create([
            'product_id' => $product->getKey(),
            'name' => 'Kumaş Türü',
            'type' => 'fabric',
        ]));
    }

    /** @return array<int, int> */
    private function fabricOptionPresetIds(ProductVariation $variation): array
    {
        return $variation->options()
            ->orderBy('sort_order')
            ->pluck('interface_fabric_type_variation_id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    private function makeFabric(string $name): InterfaceFabricTypeVariation
    {
        return InterfaceFabricTypeVariation::withoutEvents(
            fn () => InterfaceFabricTypeVariation::create(['name' => $name])
        );
    }

    public function test_fabric_can_be_assigned_to_multiple_products(): void
    {
        $fabric = $this->makeFabric('Compact Penye');
        $productA = Product::withoutEvents(fn () => Product::create(['name' => 'T-Shirt A']));
        $productB = Product::withoutEvents(fn () => Product::create(['name' => 'T-Shirt B']));

        $fabric->products()->sync([$productA->id, $productB->id]);

        $this->assertCount(2, $fabric->fresh()->products);
    }

    public function test_unassigned_fabric_is_hidden_on_every_product(): void
    {
        $unassigned = $this->makeFabric('Atanmamış Kumaş');
        $product = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün']));

        $this->assertSame([$unassigned->id], $product->hiddenFabricTypeVariationIds());
        $this->assertFalse($unassigned->isVisibleForProduct((int) $product->id));
    }

    public function test_fabric_assigned_to_other_product_is_hidden(): void
    {
        $assigned = $this->makeFabric('Sadece A Kumaşı');
        $unassigned = $this->makeFabric('Atanmamış');

        $productA = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün A']));
        $productB = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün B']));

        $assigned->products()->sync([$productA->id]);

        $this->assertSame([$unassigned->id], $productA->hiddenFabricTypeVariationIds());
        $this->assertEqualsCanonicalizing([$assigned->id, $unassigned->id], $productB->hiddenFabricTypeVariationIds());
    }

    public function test_form_imports_only_fabrics_assigned_to_the_product(): void
    {
        $onlyA = $this->makeFabric('Sadece A');
        $unassigned = $this->makeFabric('Atanmamış');

        $productA = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün A']));
        $productB = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün B']));
        $onlyA->products()->sync([$productA->id]);

        $presetIdsFor = fn (?int $productId): array => array_map(
            fn (array $row): int => (int) $row['interface_fabric_type_variation_id'],
            ProductResource::fabricVariationOptionsFromInterfacePresets($productId),
        );

        $this->assertSame([$onlyA->id], $presetIdsFor($productA->id));
        $this->assertSame([], $presetIdsFor($productB->id));
        $this->assertSame([], $presetIdsFor(null));
        $this->assertNotContains($unassigned->id, $presetIdsFor($productA->id));
    }

    public function test_reconcile_keeps_product_fabric_options_up_to_date(): void
    {
        $unassigned = $this->makeFabric('Atanmamış');
        $productA = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün A']));
        $productB = Product::withoutEvents(fn () => Product::create(['name' => 'Ürün B']));

        $variationA = $this->makeFabricVariation($productA);
        $variationB = $this->makeFabricVariation($productB);

        // Atanmamış kumaş hiçbir ürüne seçenek olarak eklenmez.
        ProductVariationOptionInterfaceSync::reconcileFabricProductOptions();
        $this->assertSame([], $this->fabricOptionPresetIds($variationA));
        $this->assertSame([], $this->fabricOptionPresetIds($variationB));
        $this->assertNotContains($unassigned->id, $this->fabricOptionPresetIds($variationA));

        // Yeni kumaş yalnızca A'ya atanırsa sadece A'nın varyasyonuna eklenir.
        $onlyA = $this->makeFabric('Sadece A');
        $onlyA->products()->sync([$productA->id]);
        ProductVariationOptionInterfaceSync::reconcileFabricProductOptions();

        $this->assertSame([$onlyA->id], $this->fabricOptionPresetIds($variationA));
        $this->assertSame([], $this->fabricOptionPresetIds($variationB));

        // Atama B'ye taşınırsa seçenek A'dan kaldırılır, B'ye eklenir.
        $onlyA->products()->sync([$productB->id]);
        ProductVariationOptionInterfaceSync::reconcileFabricProductOptions();

        $this->assertSame([], $this->fabricOptionPresetIds($variationA));
        $this->assertSame([$onlyA->id], $this->fabricOptionPresetIds($variationB));
    }
}
