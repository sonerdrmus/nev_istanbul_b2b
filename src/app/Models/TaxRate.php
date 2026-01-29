<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'tax_class_id',
        'name',
        'rate',
        'type',
        'geo_zone',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }

    public function taxClass()
    {
        return $this->belongsTo(TaxClass::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_PERCENTAGE => 'Yüzde',
            self::TYPE_FIXED => 'Sabit Tutar',
            default => $this->type,
        };
    }

    /** Belirtilen tutara vergi hesaplar. */
    public function calculate(float $amount): float
    {
        return match ($this->type) {
            self::TYPE_PERCENTAGE => round($amount * (float) $this->rate / 100, 2),
            self::TYPE_FIXED => (float) $this->rate,
            default => 0,
        };
    }
}
