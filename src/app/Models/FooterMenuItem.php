<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterMenuItem extends Model
{
    protected $fillable = [
        'footer_menu_group_id',
        'label',
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
}
