<?php

namespace App\Models;

use App\Support\LocaleContent;
use App\Support\MachineTranslator;
use App\Support\MediaUrl;
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
        'name_en',
        'slug',
        'description',
        'description_en',
        'meta_title',
        'meta_title_en',
        'meta_description',
        'meta_description_en',
        'meta_keywords',
        'price',
        'stock_quantity',
        'minimum_order_quantity',
        'image',
        'is_active',
        'status',
        'sort_order',
        'show_on_home',
        'home_showcase_order',
        'home_showcase_image',
        'size_table_trigger_variation',
        'customization_enabled',
        'customization_trigger_variation',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'customization_enabled' => 'boolean',
            'sort_order' => 'integer',
            'show_on_home' => 'boolean',
            'home_showcase_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::saving(function (Product $product): void {
            $product->autoFillEnglishFields();
        });
    }

    /**
     * Fill missing English content from Turkish via machine translation.
     */
    public function autoFillEnglishFields(bool $force = false): void
    {
        if (filled($this->name) && ($force || blank($this->name_en))) {
            $translated = MachineTranslator::translate((string) $this->name, 'tr', 'en');
            if (filled($translated)) {
                $this->name_en = $translated;
            }
        }

        if (filled($this->description) && ($force || blank($this->description_en))) {
            $translated = MachineTranslator::translateHtml((string) $this->description, 'tr', 'en');
            if (filled($translated)) {
                $this->description_en = $translated;
            }
        }

        if (filled($this->meta_title) && ($force || blank($this->meta_title_en))) {
            $translated = MachineTranslator::translate((string) $this->meta_title, 'tr', 'en');
            if (filled($translated)) {
                $this->meta_title_en = $translated;
            }
        }

        if (filled($this->meta_description) && ($force || blank($this->meta_description_en))) {
            $translated = MachineTranslator::translate((string) $this->meta_description, 'tr', 'en');
            if (filled($translated)) {
                $this->meta_description_en = $translated;
            }
        }
    }

    public function getLocalizedNameAttribute(): string
    {
        return LocaleContent::display($this->name, $this->name_en);
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        if (in_array($locale, ['en', 'it'], true) && filled($this->description_en)) {
            return $this->description_en;
        }

        return $this->description;
    }

    public function getLocalizedMetaTitleAttribute(): ?string
    {
        $locale = app()->getLocale();
        if (in_array($locale, ['en', 'it'], true) && filled($this->meta_title_en)) {
            return $this->meta_title_en;
        }

        return $this->meta_title;
    }

    public function getLocalizedMetaDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        if (in_array($locale, ['en', 'it'], true) && filled($this->meta_description_en)) {
            return $this->meta_description_en;
        }

        return $this->meta_description;
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

    /** Ürün görselleri (sıralı). Liste/öne çıkan için ilk görsel kullanılır. */
    public function productImages()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /** Slayt/liste için kullanılacak tüm görsel URL'leri: önce ana görsel, sonra ek görseller. */
    public function getDisplayImageUrlsAttribute(): array
    {
        $urls = [];
        if ($this->image) {
            $urls[] = MediaUrl::public($this->image);
        }
        foreach ($this->productImages as $img) {
            $urls[] = MediaUrl::public($img->path);
        }
        return $urls;
    }

    public function productDiscounts()
    {
        return $this->hasMany(ProductDiscount::class)->orderBy('priority')->orderBy('quantity');
    }

    public function priceTiers()
    {
        return $this->hasMany(ProductPriceTier::class)->orderBy('sort_order')->orderBy('min_quantity');
    }

    /** Bu ürüne atanmış kumaş türü varyasyonları (çoklu; pivot). */
    public function fabricTypeVariations()
    {
        return $this->belongsToMany(
            InterfaceFabricTypeVariation::class,
            'interface_fabric_type_variation_product',
            'product_id',
            'interface_fabric_type_variation_id',
        )->withTimestamps();
    }

    /** Bu ürüne atanmış ürün özelleştirme konum satırları. */
    public function customizationRows()
    {
        return $this->belongsToMany(
            ProductCustomizationRow::class,
            'product_customization_row_product',
            'product_id',
            'product_customization_row_id',
        )->withTimestamps();
    }

    public function sizeDimensionMultipliers()
    {
        return $this->hasMany(SizeDimensionMultiplier::class);
    }

    public function colorDimensionMultipliers()
    {
        return $this->hasMany(ColorDimensionMultiplier::class);
    }

    public function quantityDimensionMultipliers()
    {
        return $this->hasMany(QuantityDimensionMultiplier::class);
    }

    /**
     * Bu üründe GİZLENECEK kumaş preset id'leri: bir ürüne atanmış (pivotu olan) ama bu ürüne
     * atanmamış kumaşlar. Hiç ürüne atanmamış kumaşlar "global" sayılır ve gizlenmez.
     *
     * @return array<int, int>
     */
    public function hiddenFabricTypeVariationIds(): array
    {
        return InterfaceFabricTypeVariation::hiddenIdsForProduct((int) $this->getKey());
    }

    /**
     * Sipariş miktarına göre fiyat çarpanı (eşleşme yoksa 1).
     */
    public function resolveQuantityPriceMultiplier(int $quantity): float
    {
        $quantity = max(0, $quantity);
        $this->loadMissing('priceTiers');

        foreach ($this->priceTiers as $tier) {
            if ($tier->matchesQuantity($quantity)) {
                return $tier->normalizedMultiplier();
            }
        }

        return 1.0;
    }

    /**
     * Liste birim fiyatı (TL) = ürün fiyatı × miktar çarpanı.
     */
    public function resolveListUnitPriceInTRY(int $quantity): float
    {
        $productCurrency = $this->currency ?? Currency::getDefault();
        $baseTry = $productCurrency && $productCurrency->code !== 'TRY'
            ? $productCurrency->convertToTRY((float) $this->price)
            : (float) $this->price;

        return $baseTry * $this->resolveQuantityPriceMultiplier($quantity);
    }

    /**
     * Mağaza JS için miktar çarpanı kademeleri.
     *
     * @return list<array{min: int, max: int|null, multiplier: float}>
     */
    public function priceTiersForStore(?float $discountPercent = null): array
    {
        $this->loadMissing('priceTiers');

        return $this->priceTiers
            ->map(function (ProductPriceTier $tier): array {
                return [
                    'min' => (int) $tier->min_quantity,
                    'max' => $tier->max_quantity !== null ? (int) $tier->max_quantity : null,
                    'multiplier' => $tier->normalizedMultiplier(),
                ];
            })
            ->values()
            ->all();
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

    /** Stok adedi. Stok takibi yoksa (null) sınırsız kabul edilir. */
    public function getAvailableStock(): int
    {
        return $this->stock_quantity === null ? PHP_INT_MAX : (int) $this->stock_quantity;
    }

    /** Stokta var mı (en az 1 adet). */
    public function hasStock(): bool
    {
        return $this->getAvailableStock() > 0;
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
            'stokta_yok' => __('store.index.out_of_stock'),
            'yakinda_gelecek' => __('store.index.coming_soon_badge'),
            default => __('store.product.status_on_sale'),
        };
    }

    /** Satışa uygun mu (sepete eklenebilir). */
    public function isOnSale(): bool
    {
        return ($this->status ?? 'satista') === 'satista';
    }
}
