<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExtraVariation extends Model
{
    public const TYPE_RADIO_SINGLE = 'radio_single';

    public const TYPE_RADIO_MULTIPLE = 'radio_multiple';

    public const TYPE_TEXTAREA = 'textarea';

    public const TYPE_INFO = 'info';

    protected $fillable = [
        'title',
        'question',
        'answer_type',
        'info_text',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function options(): HasMany
    {
        return $this->hasMany(ExtraVariationOption::class)->orderBy('sort_order')->orderBy('id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ExtraVariationAssignment::class);
    }

    public function isRadio(): bool
    {
        return in_array($this->answer_type, [self::TYPE_RADIO_SINGLE, self::TYPE_RADIO_MULTIPLE], true);
    }

    public static function answerTypeOptions(): array
    {
        return [
            self::TYPE_RADIO_SINGLE => 'Radio button (tekli)',
            self::TYPE_RADIO_MULTIPLE => 'Radio button (çoklu)',
            self::TYPE_TEXTAREA => 'Textarea',
            self::TYPE_INFO => 'Sadece bilgilendirme',
        ];
    }
}
