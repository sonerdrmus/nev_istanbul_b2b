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

    /**
     * Footer links saved from the local Docker site stay pointed at localhost.
     * Keep the path and serve it on the current store host.
     */
    public function storefrontUrl(): string
    {
        $url = trim((string) $this->url);
        if ($url === '' || $url === '#') {
            return $url;
        }

        $parts = parse_url($url);
        if (! is_array($parts) || ! isset($parts['host'])) {
            return $url;
        }

        $host = strtolower((string) $parts['host']);
        if (! in_array($host, ['localhost', '127.0.0.1', '0.0.0.0'], true)) {
            return $url;
        }

        $path = ($parts['path'] ?? '') !== '' ? $parts['path'] : '/';
        $query = isset($parts['query']) ? '?'.$parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#'.$parts['fragment'] : '';

        return url($path).$query.$fragment;
    }
}
