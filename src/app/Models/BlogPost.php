<?php

namespace App\Models;

use App\Support\LocaleContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'title_en',
        'title_it',
        'excerpt',
        'excerpt_en',
        'excerpt_it',
        'body',
        'body_en',
        'body_it',
        'image_path',
        'is_published',
        'published_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (BlogPost $post): void {
            if ($post->published_at === null) {
                $post->published_at = now();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where(function (Builder $inner): void {
                $inner->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function isPublic(): bool
    {
        if (! $this->is_published) {
            return false;
        }

        return $this->published_at === null || $this->published_at->lte(now());
    }

    public function getLocalizedTitleAttribute(): string
    {
        return LocaleContent::display($this->title, $this->title_en, $this->title_it);
    }

    public function getLocalizedBodyAttribute(): string
    {
        return LocaleContent::display($this->body, $this->body_en, $this->body_it);
    }

    public function getLocalizedExcerptAttribute(): string
    {
        $excerpt = trim(LocaleContent::display($this->excerpt, $this->excerpt_en, $this->excerpt_it));
        if ($excerpt !== '') {
            return $excerpt;
        }

        $plain = trim(preg_replace(
            '/\s+/u',
            ' ',
            html_entity_decode(strip_tags($this->localized_body), ENT_QUOTES | ENT_HTML5, 'UTF-8')
        ) ?? '');

        return Str::limit($plain, 160);
    }
}
