<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomizationChoice extends Model
{
    public const NONE_LABEL = 'Hiçbirini istemiyorum';

    protected $fillable = [
        'title',
        'description',
        'allows_multiple',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'allows_multiple' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function options(): HasMany
    {
        return $this->hasMany(CustomizationChoiceOption::class)->orderBy('sort_order')->orderBy('id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(CustomizationChoiceAssignment::class);
    }
}
