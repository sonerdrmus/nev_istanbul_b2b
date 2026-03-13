<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizeTable extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'title',
        'trigger_variation_name',
        'trigger_option_value',
        'sort_order',
    ];

    public function columns()
    {
        return $this->hasMany(SizeTableColumn::class)->orderBy('sort_order');
    }
}
