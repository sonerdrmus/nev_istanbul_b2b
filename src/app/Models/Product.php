<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'company_id',
        'category_id',
        'tax_class_id',
        'currency_id',
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'price',
        'stock_quantity',
        'minimum_order_quantity',
        'image',
        'is_active',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function taxClass()
    {
        return $this->belongsTo(TaxClass::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /** Fiyatı para birimine göre formatlar */
    public function getFormattedPriceAttribute(): string
    {
        $currency = $this->currency ?? Currency::getDefault();
        if (! $currency) {
            return number_format((float) $this->price, 2, ',', '.') . ' ₺';
        }
        return $currency->format((float) $this->price);
    }

    /**
     * Fiyatı belirtilen para birimine çevirir ve formatlar.
     * Müşteri kâr marjı (indirim) varsa discountPercent ile uygulanır (örn: 10 = %10 indirim).
     */
    public function getPriceInCurrency(?Currency $targetCurrency = null, ?float $discountPercent = null): string
    {
        $targetCurrency = $targetCurrency ?? Currency::getDefault();
        if (! $targetCurrency) {
            return $this->formatted_price;
        }

        $productCurrency = $this->currency ?? Currency::getDefault();
        if (! $productCurrency) {
            return number_format((float) $this->price, 2, ',', '.') . ' ₺';
        }

        // Ürün fiyatı TL cinsinden (productCurrency TRY ise direkt, değilse TL'ye çevir)
        $priceInTRY = $productCurrency->code === 'TRY'
            ? (float) $this->price
            : $productCurrency->convertToTRY((float) $this->price);

        // Müşteri kâr marjı (indirim) uygula: %10 marj = fiyatın %10'u indirim
        if ($discountPercent !== null && $discountPercent > 0) {
            $priceInTRY = $priceInTRY * (1 - $discountPercent / 100);
        }

        // Hedef para birimine çevir
        $convertedPrice = $targetCurrency->convertFromTRY($priceInTRY);

        return $targetCurrency->format($convertedPrice);
    }

    /** TL cinsinden birim fiyat (müşteri indirimi uygulanmış, opsiyonel) */
    public function getPriceInTRY(?float $discountPercent = null): float
    {
        $productCurrency = $this->currency ?? Currency::getDefault();
        $priceInTRY = ! $productCurrency || $productCurrency->code === 'TRY'
            ? (float) $this->price
            : $productCurrency->convertToTRY((float) $this->price);
        if ($discountPercent !== null && $discountPercent > 0) {
            $priceInTRY = $priceInTRY * (1 - $discountPercent / 100);
        }
        return $priceInTRY;
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class)->orderBy('sort_order');
    }

    public function productDiscounts()
    {
        return $this->hasMany(ProductDiscount::class)->orderBy('priority')->orderBy('quantity');
    }

    /**
     * Miktar ve müşteri grubuna göre geçerli indirimli birim fiyatı TL cinsinden döndürür.
     * Tarih aralığına uyan, quantity >= rule.quantity olan kurallar içinden önceliğe ve miktara göre en uygun kural seçilir.
     *
     * @return float|null TL cinsinden indirimli birim fiyat veya uygulanabilir kural yoksa null
     */
    public function getDiscountUnitPriceInTRY(int $quantity, int $customerGroupId, ?\DateTimeInterface $date = null): ?float
    {
        $date = $date ?? now();
        $productCurrency = $this->currency ?? Currency::getDefault();
        $rules = $this->productDiscounts()
            ->where('customer_group_id', $customerGroupId)
            ->where('quantity', '<=', $quantity)
            ->orderBy('priority')
            ->orderByDesc('quantity')
            ->get();
        foreach ($rules as $rule) {
            if (! $rule->isActiveAt($date)) {
                continue;
            }
            $priceInProductCurrency = (float) $rule->price;
            $priceInTRY = $productCurrency && $productCurrency->code !== 'TRY'
                ? $productCurrency->convertToTRY($priceInProductCurrency)
                : $priceInProductCurrency;
            return $priceInTRY;
        }
        return null;
    }

    public function hasVariations(): bool
    {
        return $this->variations()->exists();
    }

    /**
     * Seçilen varyasyon kombinasyonu için stok adedi.
     * Stok takibi yoksa (null) sınırsız döner. 0 = stokta yok.
     */
    public function getAvailableStock(?array $variations = null): int
    {
        $variations = $variations ?? [];
        if (empty($variations)) {
            return $this->stock_quantity === null ? PHP_INT_MAX : (int) $this->stock_quantity;
        }
        $this->loadMissing('variations.optionPrices');
        $stocks = [];
        foreach ($variations as $varName => $optionValue) {
            $variation = $this->variations->firstWhere('name', (string) $varName);
            if (! $variation) {
                continue;
            }
            $row = $variation->optionPrices->firstWhere('option_value', (string) $optionValue);
            if ($row !== null && $row->stock_quantity !== null && $row->stock_quantity !== '') {
                $stocks[] = (int) $row->stock_quantity;
            }
        }
        if ($stocks !== []) {
            return min($stocks);
        }
        return $this->stock_quantity === null ? PHP_INT_MAX : (int) $this->stock_quantity;
    }

    /** Stokta var mı (en az 1 adet). */
    public function hasStock(?array $variations = null): bool
    {
        return $this->getAvailableStock($variations) > 0;
    }

    /** Sipariş edilebilir minimum miktar (varsayılan 1). */
    public function getMinimumOrderQuantity(): int
    {
        $min = $this->minimum_order_quantity;
        return $min !== null && $min > 0 ? (int) $min : 1;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Sadece satışta olan ürünler (sepete eklenebilir). */
    public function scopeOnSale($query)
    {
        return $query->where('status', 'satista');
    }

    /** Mağaza listesinde görünen ürünler: Satışta + Yakında gelecek (Stokta yok hariç). */
    public function scopeVisibleInStore($query)
    {
        return $query->whereIn('status', ['satista', 'yakinda_gelecek']);
    }

    /** Ürün durumu etiketi. */
    public function getStatusLabel(): string
    {
        return match ($this->status ?? 'satista') {
            'stokta_yok' => 'Stokta yok',
            'yakinda_gelecek' => 'Yakında gelecek',
            default => 'Satışta',
        };
    }

    /** Satışa uygun mu (sepete eklenebilir). */
    public function isOnSale(): bool
    {
        return ($this->status ?? 'satista') === 'satista';
    }
}
