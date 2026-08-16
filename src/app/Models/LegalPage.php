<?php

namespace App\Models;

use App\Support\LocaleContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LegalPage extends Model
{
    public const PRIVACY_SLUG = 'gizlilik-politikasi';

    public const TERMS_SLUG = 'kullanim-kosullari';

    public const CONTACT_SLUG = 'iletisim';

    public const COOKIE_SLUG = 'cerez-politikasi';

    public const SALES_SLUG = 'b2b-satis-kosullari';

    public const DELIVERY_SLUG = 'teslimat-ve-kargo';

    public const RETURNS_SLUG = 'iade-ve-talepler';

    public const DATA_PROTECTION_SLUG = 'veri-koruma';

    public const PAYMENT_SLUG = 'odeme-kosullari';

    protected $fillable = [
        'slug',
        'title',
        'title_en',
        'title_it',
        'body',
        'body_en',
        'body_it',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getLocalizedTitleAttribute(): string
    {
        return LocaleContent::display(
            $this->title ?? null,
            $this->title_en ?? null,
            $this->title_it ?? null,
        );
    }

    public function getLocalizedBodyAttribute(): string
    {
        return static::inlineBareClauseNumbers(LocaleContent::display(
            $this->body ?? null,
            $this->body_en ?? null,
            $this->body_it ?? null,
        ));
    }

    /** Put bare clause numbers (e.g. 2.8) on the same line as the following paragraph. */
    public static function inlineBareClauseNumbers(string $html): string
    {
        $merged = preg_replace(
            '/<h3>\s*(\d+(?:\.\d+)*)\s*<\/h3>\s*<p>/iu',
            '<p><strong>$1</strong> ',
            $html
        );
        $html = is_string($merged) ? $merged : $html;

        $merged = preg_replace(
            '/<p>\s*<strong>\s*(\d+(?:\.\d+)*)\s*<\/strong>\s*<\/p>\s*<p>/iu',
            '<p><strong>$1</strong> ',
            $html
        );

        return is_string($merged) ? $merged : $html;
    }

    public function storeUrl(): string
    {
        return route('store.legal.show', $this);
    }

    public static function privacyUrl(): ?string
    {
        $page = static::query()->published()->where('slug', self::PRIVACY_SLUG)->first();

        return $page?->storeUrl();
    }
}
