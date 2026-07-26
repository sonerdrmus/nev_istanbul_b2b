<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\ProductPriceTier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPriceTiersTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(float $basePrice = 50): Product
    {
        $company = Company::create([
            'name' => 'Tier Co',
            'code' => 'TIER-CO',
            'is_active' => true,
        ]);

        return Product::create([
            'company_id' => $company->id,
            'name' => 'Tier Product',
            'slug' => 'tier-product-'.uniqid(),
            'price' => $basePrice,
            'status' => 'satista',
            'is_active' => true,
            'minimum_order_quantity' => 1,
            'stock_quantity' => 1000,
        ]);
    }

    public function test_quantity_multiplier_matches_ranges_and_scales_product_price(): void
    {
        $product = $this->makeProduct(50);

        ProductPriceTier::create([
            'product_id' => $product->id,
            'min_quantity' => 1,
            'max_quantity' => 100,
            'price_multiplier' => 1,
            'sort_order' => 0,
        ]);
        ProductPriceTier::create([
            'product_id' => $product->id,
            'min_quantity' => 101,
            'max_quantity' => 200,
            'price_multiplier' => 2,
            'sort_order' => 1,
        ]);
        ProductPriceTier::create([
            'product_id' => $product->id,
            'min_quantity' => 201,
            'max_quantity' => null,
            'price_multiplier' => 1.5,
            'sort_order' => 2,
        ]);

        $product->refresh()->load('priceTiers');

        $this->assertSame(1.0, $product->resolveQuantityPriceMultiplier(1));
        $this->assertSame(1.0, $product->resolveQuantityPriceMultiplier(100));
        $this->assertSame(2.0, $product->resolveQuantityPriceMultiplier(101));
        $this->assertSame(2.0, $product->resolveQuantityPriceMultiplier(200));
        $this->assertSame(1.5, $product->resolveQuantityPriceMultiplier(201));

        $this->assertEqualsWithDelta(50.0, $product->resolveListUnitPriceInTRY(50), 0.01);
        $this->assertEqualsWithDelta(100.0, $product->resolveListUnitPriceInTRY(150), 0.01);
        $this->assertEqualsWithDelta(75.0, $product->resolveListUnitPriceInTRY(250), 0.01);
    }

    public function test_resolve_list_unit_price_falls_back_to_product_price_when_no_tier(): void
    {
        $product = $this->makeProduct(75.5);
        $product->load('priceTiers');

        $this->assertSame(1.0, $product->resolveQuantityPriceMultiplier(10));
        $this->assertSame(75.5, $product->resolveListUnitPriceInTRY(10));
    }

    public function test_cart_uses_quantity_multiplier_on_product_price(): void
    {
        $product = $this->makeProduct(50);
        ProductPriceTier::create([
            'product_id' => $product->id,
            'min_quantity' => 1,
            'max_quantity' => 100,
            'price_multiplier' => 1,
            'sort_order' => 0,
        ]);
        ProductPriceTier::create([
            'product_id' => $product->id,
            'min_quantity' => 101,
            'max_quantity' => 200,
            'price_multiplier' => 2,
            'sort_order' => 1,
        ]);

        $user = User::factory()->create([
            'company_id' => $product->company_id,
            'email' => 'tier-buyer@example.com',
        ]);

        $this->actingAs($user)
            ->post(route('store.cart.add'), [
                'product_id' => $product->id,
                'quantity' => 150,
            ])
            ->assertRedirect(route('store.cart'));

        $controller = app(\App\Http\Controllers\StoreController::class);
        $method = new \ReflectionMethod($controller, 'getCartItems');
        $method->setAccessible(true);
        /** @var \Illuminate\Support\Collection $items */
        $items = $method->invoke($controller);

        $this->assertCount(1, $items);
        $item = $items->first();
        $this->assertSame(150, (int) $item->quantity);
        // 50 × 2 = 100 birim; 100 × 150 = 15000
        $this->assertEqualsWithDelta(100.0, (float) $item->unit_price_try, 0.01);
        $this->assertEqualsWithDelta(15000.0, (float) $item->subtotal, 0.01);
    }
}
