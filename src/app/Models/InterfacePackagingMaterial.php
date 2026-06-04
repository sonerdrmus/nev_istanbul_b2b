<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterfacePackagingMaterial extends Model
{
    protected $table = 'interface_packaging_materials';

    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
