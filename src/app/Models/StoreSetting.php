<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'show_price_multipliers',
        'show_print_total',
        'show_print_price',
        'show_print_card_total',
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
        ]);
    }

    public static function showsPriceMultipliers(): bool
    {
        return (bool) static::current()->show_price_multipliers;
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
