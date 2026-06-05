<?php

namespace App\Models;

use App\Models\Concerns\SyncsLinkedProductVariationOptions;
use Illuminate\Database\Eloquent\Model;

class SizeTable extends Model
{
    use SyncsLinkedProductVariationOptions;

    protected $fillable = [
        'name',
        'slug',
        'title',
        'trigger_variation_name',
        'trigger_option_value',
        'sort_order',
    ];

    protected static function linkedProductVariationType(): string
    {
        return 'size_table';
    }

    public function productVariationOptions()
    {
        return $this->hasMany(ProductVariationOption::class, 'size_table_id');
    }

    public function columns()
    {
        return $this->hasMany(SizeTableColumn::class)->orderBy('sort_order');
    }
}
