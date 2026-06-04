<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterfacePackagingCustomization extends Model
{
    protected $table = 'interface_packaging_customizations';

    protected $fillable = [
        'name',
        'slug',
        'extra_price',
        'is_default',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'extra_price' => 'decimal:2',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
