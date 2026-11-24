<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class StoreSetting extends Model
{
    protected $fillable = [
        'show_price_multipliers',
        'show_print_total',
        'show_print_price',
        'show_print_card_total',
        'media_cdn_url',
    ];

    protected function casts(): array
    {
        return [
            'show_price_multipliers' => 'boolean',
            'show_print_total' => 'boolean',
            'show_print_price' => 'boolean',
            'show_print_card_total' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'show_price_multipliers' => false,
            'show_print_total' => false,
            'show_print_price' => false,
            'show_print_card_total' => false,
            'media_cdn_url' => null,
        ]);
    }

    /**
     * Medya Storage ayarı → store_settings → .env MEDIA_CDN_URL.
     */
    public static function mediaCdnUrl(): string
    {
        if (MediaStorageSetting::tableReady()) {
            $fromMedia = MediaStorageSetting::current()->resolvedPublicUrl();
            if ($fromMedia !== '') {
                return $fromMedia;
            }
        }

        if (static::hasMediaCdnColumn()) {
            $fromDb = trim((string) (static::current()->media_cdn_url ?? ''));
            if ($fromDb !== '') {
                return rtrim($fromDb, '/');
            }
        }

        return rtrim(trim((string) config('store.media_cdn_url', '')), '/');
    }

    public static function hasMediaCdnColumn(): bool
    {
        try {
            return Schema::hasTable('store_settings')
                && Schema::hasColumn('store_settings', 'media_cdn_url');
        } catch (\Throwable) {
            return false;
        }
    }

    public static function showsPriceMultipliers(): bool
    {
        if (! static::current()->show_price_multipliers) {
            return false;
        }

        return (bool) auth()->user()?->is_admin;
    }

    public static function showsPrintTotal(): bool
    {
        return (bool) static::current()->show_print_total;
    }

    public static function showsPrintPrice(): bool
    {
        return (bool) static::current()->show_print_price;
    }

    public static function showsPrintCardTotal(): bool
    {
        return (bool) static::current()->show_print_card_total;
    }
}
