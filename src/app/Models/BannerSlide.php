<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerSlide extends Model
{
    protected $fillable = [
        'image_path',
        'title',
        'headline',
        'description',
        'button_text',
        'button_url',
        'text_align',
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
