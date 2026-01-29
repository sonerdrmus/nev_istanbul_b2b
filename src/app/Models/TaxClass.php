<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxClass extends Model
{
    protected $fillable = [
        'title',
        'sort_order',
    ];

    public function taxRates()
    {
        return $this->hasMany(TaxRate::class)->orderBy('sort_order');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'tax_class_id');
    }
}
