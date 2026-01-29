<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    protected $fillable = [
        'image_path',
        'label',
        'title',
        'subtitle',
        'button_text',
        'link_url',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public static function forHome(): \Illuminate\Database\Eloquent\Builder
    {
        return static::where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
