<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'code', 'is_active', 'profit_margin_percentage'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'profit_margin_percentage' => 'decimal:2',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
