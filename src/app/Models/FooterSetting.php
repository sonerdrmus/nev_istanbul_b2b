<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterSetting extends Model
{
    protected $fillable = [
        'columns',
        'show_brand',
    ];

    protected function casts(): array
    {
        return [
            'show_brand' => 'boolean',
        ];
    }

    public static function get(): self
    {
        return static::firstOrCreate([], [
            'columns' => 4,
            'show_brand' => true,
        ]);
    }
}
