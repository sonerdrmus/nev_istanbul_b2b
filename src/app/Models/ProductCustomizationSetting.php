<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCustomizationSetting extends Model
{
    protected $fillable = [
        'max_color_count',
        'default_print_technique_slug',
    ];

    protected function casts(): array
    {
        return [
            'max_color_count' => 'integer',
        ];
    }

    public static function instance(): self
    {
        return static::query()->firstOrCreate([], [
            'max_color_count' => 7,
            'default_print_technique_slug' => 'emprime',
        ]);
    }
}
