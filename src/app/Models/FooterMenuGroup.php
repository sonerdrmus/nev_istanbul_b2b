<?php

namespace App\Models;

use App\Support\LocaleContent;
use Illuminate\Database\Eloquent\Model;

class FooterMenuGroup extends Model
{
    public const TYPE_MENU = 'menu';
    public const TYPE_CATEGORIES = 'categories';
    public const TYPE_BANK_INFO = 'bank_info';

    protected $fillable = [
        'title',
        'title_en',
        'title_it',
        'type',
        'sort_order',
    ];

    protected $attributes = [
        'type' => self::TYPE_MENU,
    ];

    public function items()
    {
        return $this->hasMany(FooterMenuItem::class)->orderBy('sort_order');
    }

    public function getLocalizedTitleAttribute(): string
    {
        return LocaleContent::display($this->title, $this->title_en, $this->title_it);
    }
}
