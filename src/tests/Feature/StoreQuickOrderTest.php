<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoreQuickOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

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
            'quick_order_images' => [
                UploadedFile::fake()->image('a.jpg'),
                UploadedFile::fake()->image('b.png'),
            ],
        ]);

        $response->assertRedirect(route('store.cart'));
        $response->assertSessionHas('success');

        $cart = session('cart');
        $this->assertArrayHasKey((string) $product->id, $cart);
        $quick = collect($cart)->first()['quick_order'] ?? null;
        $this->assertNotEmpty($quick);
        $this->assertCount(2, $quick['images']);
        $this->assertSame($quick['images'][0]['path'], $quick['image_path']);
        $this->assertSame($quick['images'][0]['url'], $quick['image_url']);
        Storage::disk('public')->assertExists($quick['image_path']);
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
            'quick_order_images' => [UploadedFile::fake()->image('a.jpg')],
        ]);

        $response->assertRedirect(route('store.cart'));
        $response->assertSessionHas('success');

        $missing = $this->post(route('store.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
            'order_mode' => 'quick',
            'quick_order_notes' => 'Görselsiz not',
        ]);
        $missing->assertSessionHas('error');

        $cart = session('cart');
        $this->assertArrayHasKey((string) $product->id, $cart);
        $this->assertNotEmpty($cart[(string) $product->id]['quick_order'] ?? null);
    }

    public function test_products_without_stock_can_still_be_added_to_cart(): void
    {
        $company = Company::create([
            'name' => 'Stockless Company',
            'code' => 'STOCKLESS',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'email' => 'stockless@example.com',
        ]);

        $product = Product::create([
            'company_id' => $company->id,
            'name' => 'Stockless Product',
            'slug' => 'stockless-product',
            'price' => 100,
            'status' => 'stokta_yok',
            'is_active' => true,
            'minimum_order_quantity' => 1,
            'stock_quantity' => 0,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('store.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertRedirect(route('store.cart'));
        $response->assertSessionHas('success');

        $this->assertArrayHasKey((string) $product->id, session('cart', []));
    }
}
