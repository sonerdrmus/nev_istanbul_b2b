<?php

namespace App\Models;

use App\Support\LocaleContent;
use Illuminate\Database\Eloquent\Model;

class BannerSlide extends Model
{
    protected $fillable = [
        'image_path',
        'title',
        'title_en',
        'title_it',
        'headline',
        'headline_en',
        'headline_it',
        'description',
        'description_en',
        'description_it',
        'button_text',
        'button_text_en',
        'button_text_it',
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

    public function getLocalizedTitleAttribute(): string
    {
        return LocaleContent::display($this->title, $this->title_en, $this->title_it);
    }

    public function getLocalizedHeadlineAttribute(): string
    {
        return LocaleContent::display($this->headline, $this->headline_en, $this->headline_it);
    }

    public function getLocalizedDescriptionAttribute(): string
    {
        return LocaleContent::display($this->description, $this->description_en, $this->description_it);
    }

    public function getLocalizedButtonTextAttribute(): string
    {
        return LocaleContent::display($this->button_text, $this->button_text_en, $this->button_text_it);
    }

    public static function forHome(): \Illuminate\Database\Eloquent\Builder
    {
        return static::where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
