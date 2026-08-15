<?php

namespace App\Models;

use App\Models\Concerns\FillsLocalizedNameFromCatalog;
use App\Models\Concerns\HasLocalizedName;
use Illuminate\Database\Eloquent\Model;

class InterfacePackagingMaterial extends Model
{
    use FillsLocalizedNameFromCatalog;
    use HasLocalizedName;
    protected $table = 'interface_packaging_materials';

    protected $fillable = [
        'name',
        'name_en',
        'name_it',
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
