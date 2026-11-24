<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class MediaStorageSetting extends Model
{
    protected $fillable = [
        'enabled',
        'provider',
        'name',
        'location',
        'bucket',
        'endpoint',
        'region',
        'access_key_id',
        'secret_access_key',
        'public_url',
        'use_path_style_endpoint',
        'use_for_uploads',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'use_path_style_endpoint' => 'boolean',
            'use_for_uploads' => 'boolean',
            'secret_access_key' => 'encrypted',
        ];
    }

    public static function tableReady(): bool
    {
        try {
            return Schema::hasTable('media_storage_settings');
        } catch (\Throwable) {
            return false;
        }
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'enabled' => false,
            'provider' => 'r2',
            'region' => 'auto',
            'use_path_style_endpoint' => true,
            'use_for_uploads' => false,
        ]);
    }

    public function isConfigured(): bool
    {
        if (! $this->enabled) {
            return false;
        }

        return filled($this->bucket)
            && filled($this->endpoint)
            && filled($this->access_key_id)
            && filled($this->secret_access_key);
    }

    /**
     * Tarayıcı / preconnect için genel medya kök URL.
     */
    public function resolvedPublicUrl(): string
    {
        $url = trim((string) ($this->public_url ?? ''));
        if ($url !== '') {
            return rtrim($url, '/');
        }

        return '';
    }

    /**
     * S3 API endpoint (bucket path olmadan).
     */
    public function normalizedEndpoint(): ?string
    {
        $endpoint = trim((string) ($this->endpoint ?? ''));
        if ($endpoint === '') {
            return null;
        }

        $endpoint = rtrim($endpoint, '/');
        $bucket = trim((string) ($this->bucket ?? ''));
        if ($bucket !== '' && str_ends_with(strtolower($endpoint), '/'.strtolower($bucket))) {
            $endpoint = substr($endpoint, 0, -strlen($bucket) - 1);
        }

        return rtrim($endpoint, '/');
    }
}
