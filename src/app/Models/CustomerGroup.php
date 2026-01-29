<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function productDiscounts()
    {
        return $this->hasMany(ProductDiscount::class);
    }
}
