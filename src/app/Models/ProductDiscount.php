<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDiscount extends Model
{
    protected $fillable = [
        'product_id',
        'customer_group_id',
        'quantity',
        'priority',
        'price',
        'date_start',
        'date_end',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'priority' => 'integer',
            'price' => 'decimal:4',
            'date_start' => 'date',
            'date_end' => 'date',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    /** Bugünün tarihi bu indirim kuralının geçerli olduğu aralıkta mı? */
    public function isActiveAt(?\DateTimeInterface $date = null): bool
    {
        $date = $date ?? now();
        if ($this->date_start && $date->format('Y-m-d') < $this->date_start->format('Y-m-d')) {
            return false;
        }
        if ($this->date_end && $date->format('Y-m-d') > $this->date_end->format('Y-m-d')) {
            return false;
        }
        return true;
    }
}
