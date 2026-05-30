<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class QuantityDimensionMultiplier extends Model
{
    protected $fillable = [
        'quantity_from',
        'quantity_to',
        'multiplier_price',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'quantity_from' => 'integer',
            'quantity_to' => 'integer',
            'multiplier_price' => 'decimal:4',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /** @return Collection<int, static> */
    public static function activeOrdered(): Collection
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('quantity_from')
            ->get();
    }

    public function rangeLabel(): string
    {
        if ($this->quantity_to >= 9999) {
            return $this->quantity_from.'+';
        }

        return $this->quantity_from.' - '.$this->quantity_to;
    }

    /** @return array<string, mixed> */
    public static function repeaterRowFromModel(self $row): array
    {
        return [
            'id' => $row->id,
            'quantity_from' => $row->quantity_from,
            'quantity_to' => $row->quantity_to,
            'multiplier_price' => $row->multiplier_price,
            'sort_order' => $row->sort_order,
            'is_active' => $row->is_active,
        ];
    }
}
