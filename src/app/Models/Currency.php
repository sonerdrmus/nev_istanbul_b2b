<?php

namespace App\Models;

use App\Services\TcmbExchangeRateService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate',
        'decimal_places',
        'is_default',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'exchange_rate' => 'decimal:4',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Currency $currency): void {
            if ($currency->is_default) {
                static::where('id', '!=', $currency->id)->update(['is_default' => false]);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Fiyatı bu para birimine göre formatlar (örn. 1.234,56 ₺) */
    public function format(float $amount): string
    {
        $formatted = number_format(
            $amount,
            $this->decimal_places,
            ',',
            '.'
        );

        return $formatted . ' ' . $this->symbol;
    }

    /** Varsayılan para birimini döndürür */
    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->first()
            ?? static::active()->orderBy('sort_order')->first();
    }

    /**
     * Aktif kullanıcıya göre para birimleri; USD/EUR kurları TCMB today.xml ile (kısa cache) güncellenir.
     *
     * @return Collection<int, self>
     */
    public static function forCurrentUserWithTcmbSpot(): Collection
    {
        $base = static::forCurrentUser();

        return app(TcmbExchangeRateService::class)->mergeUsdEurInto($base);
    }

    /** Mağazada giriş yapmış bayi için izin verilen para birimleri; misafir veya admin için tüm aktif para birimleri. */
    public static function forCurrentUser(): Collection
    {
        $base = static::active()->orderBy('sort_order');
        if (! auth()->check()) {
            return $base->get();
        }
        $user = auth()->user();
        if ($user instanceof User && $user->is_admin) {
            return $base->get();
        }
        $ids = $user instanceof User ? $user->visible_currency_ids : null;
        if ($ids === null || $ids === []) {
            return $base->get();
        }

        return $base->whereIn('id', $ids)->get();
    }

    /** TL tutarını bu para birimine çevirir (exchange_rate kullanarak) */
    public function convertFromTRY(float $tryAmount): float
    {
        if ($this->code === 'TRY') {
            return $tryAmount;
        }
        return $tryAmount / $this->exchange_rate;
    }

    /** Bu para birimindeki tutarı TL'ye çevirir */
    public function convertToTRY(float $amount): float
    {
        if ($this->code === 'TRY') {
            return $amount;
        }
        return $amount * $this->exchange_rate;
    }
}
