<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomizationChoiceOption extends Model
{
    public const IMAGE_SIZES = ['small', 'medium', 'large'];

    protected $fillable = [
        'customization_choice_id',
        'label',
        'image_path',
        'image_size',
        'sort_order',
    ];

    public function normalizedImageSize(): string
    {
        return in_array($this->image_size, self::IMAGE_SIZES, true) ? $this->image_size : 'medium';
    }

    public function customizationChoice(): BelongsTo
    {
        return $this->belongsTo(CustomizationChoice::class);
    }
}
