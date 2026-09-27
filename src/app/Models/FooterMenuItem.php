<?php

namespace App\Models;

use App\Support\LocaleContent;
use Illuminate\Database\Eloquent\Model;

class FooterMenuItem extends Model
{
    protected $fillable = [
        'footer_menu_group_id',
        'label',
        'label_en',
        'label_it',
        'url',
        'open_in_new_tab',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'open_in_new_tab' => 'boolean',
        ];
    }

    public function group()
    {
        return $this->belongsTo(FooterMenuGroup::class, 'footer_menu_group_id');
    }

    public function getLocalizedLabelAttribute(): string
    {
        return LocaleContent::display($this->label, $this->label_en, $this->label_it);
    }
}
