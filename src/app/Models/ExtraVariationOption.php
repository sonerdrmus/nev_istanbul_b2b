<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraVariationOption extends Model
{
    protected $fillable = [
        'extra_variation_id',
        'label',
        'sort_order',
    ];

    public function extraVariation(): BelongsTo
    {
        return $this->belongsTo(ExtraVariation::class);
    }
}
