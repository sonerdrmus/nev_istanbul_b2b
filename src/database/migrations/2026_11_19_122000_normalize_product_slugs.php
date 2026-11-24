<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Product::query()->orderBy('id')->each(function (Product $product): void {
            $desired = filled($product->slug) ? (string) $product->slug : (string) $product->name;
            $normalized = Product::uniqueSlugForCompany(
                $desired,
                $product->company_id !== null ? (int) $product->company_id : null,
                (int) $product->getKey(),
            );

            // Boşluklu / eski slug'ları URL-safe hale getir.
            if ($normalized !== (string) $product->slug || Str::slug((string) $product->slug) !== (string) $product->slug) {
                $product->forceFill(['slug' => $normalized])->saveQuietly();
            }
        });
    }

    public function down(): void
    {
        // Geri alınmaz.
    }
};
