<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterfacePackagingSetting extends Model
{
    protected $table = 'interface_packaging_settings';

    protected $fillable = [
        'barcode_enabled',
        'barcode_label',
        'barcode_extra_price',
        'barcode_description',
        'barcode_image_path',
    ];

    protected function casts(): array
    {
        return [
            'barcode_enabled' => 'boolean',
            'barcode_extra_price' => 'decimal:2',
        ];
    }

    public static function instance(): self
    {
        return static::query()->firstOrCreate([], [
            'barcode_enabled' => true,
            'barcode_label' => 'Barkod / Etiket Alanı İstiyorum',
            'barcode_extra_price' => 0,
            'barcode_description' => null,
            'barcode_image_path' => null,
        ]);
    }
}
