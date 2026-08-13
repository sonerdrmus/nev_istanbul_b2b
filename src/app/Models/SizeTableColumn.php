<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizeTableColumn extends Model
{
    protected $fillable = [
        'size_table_id',
        'size_value',
        'sort_order',
        'price_multiplier',
    ];

    protected function casts(): array
    {
        return [
            'price_multiplier' => 'decimal:3',
        ];
    }

    public function sizeTable()
    {
        return $this->belongsTo(SizeTable::class);
    }
}
