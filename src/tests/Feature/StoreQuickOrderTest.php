<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreQuickOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_quick_order_can_be_added_to_cart_when_only_quick_fields_are_present(): void
    {
        $company = Company::create([
            'name' => 'Test Company',
            'code' => 'TEST-COMPANY',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'email' => 'test@example.com',
        ]);

        $product = Product::create([
            'company_id' => $company->id,
            'name' => 'Test Quick Product 2',
            'slug' => 'test-quick-product-2',
            'price' => 100,
            'status' => 'satista',
            'is_active' => true,
            'minimum_order_quantity' => 1,
            'stock_quantity' => 10,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('store.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
            'quick_order_notes' => 'Bu metin hızlı sipariş olarak kabul edilmelidir.',
        ]);

        $response->assertRedirect(route('store.cart'));
        $response->assertSessionHas('success');

        $cart = session('cart');
        $this->assertArrayHasKey((string) $product->id, $cart);
        $this->assertNotEmpty($cart[(string) $product->id]['quick_order'] ?? null);
    }

    public function test_quick_order_can_be_added_to_cart_with_short_note(): void
    {
        $company = Company::create([
            'name' => 'Test Company',
            'code' => 'TEST-COMPANY',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'email' => 'test@example.com',
        ]);

        $product = Product::create([
            'company_id' => $company->id,
            'name' => 'Test Quick Product',
            'slug' => 'test-quick-product',
            'price' => 100,
            'status' => 'satista',
            'is_active' => true,
            'minimum_order_quantity' => 1,
            'stock_quantity' => 10,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('store.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
            'order_mode' => 'quick',
            'quick_order_notes' => 'Kısa not',
        ]);

        $response->assertRedirect(route('store.cart'));
        $response->assertSessionHas('success');

        $cart = session('cart');
        $this->assertArrayHasKey((string) $product->id, $cart);
        $this->assertNotEmpty($cart[(string) $product->id]['quick_order'] ?? null);
    }
}
