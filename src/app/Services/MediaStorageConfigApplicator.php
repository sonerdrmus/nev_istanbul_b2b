<?php

namespace App\Services;

use App\Models\MediaStorageSetting;
use Illuminate\Support\Facades\Config;

class MediaStorageConfigApplicator
{
    public function apply(?MediaStorageSetting $settings = null): bool
    {
        if (! MediaStorageSetting::tableReady()) {
            return false;
        }

        $settings ??= MediaStorageSetting::query()->first();
        if (! $settings || ! $settings->isConfigured()) {
            return false;
        }

        $endpoint = $settings->normalizedEndpoint();
        $publicUrl = $settings->resolvedPublicUrl();
        $region = trim((string) ($settings->region ?: 'auto')) ?: 'auto';

        $s3 = [
            'driver' => 's3',
            'key' => $settings->access_key_id,
            'secret' => $settings->secret_access_key,
            'region' => $region,
            'bucket' => $settings->bucket,
            'url' => $publicUrl !== '' ? $publicUrl : null,
            'endpoint' => $endpoint,
            'use_path_style_endpoint' => (bool) $settings->use_path_style_endpoint,
            'throw' => false,
            'report' => false,
            'visibility' => 'public',
        ];

        Config::set('filesystems.disks.s3', array_merge(config('filesystems.disks.s3', []), $s3));

        if ($publicUrl !== '') {
            Config::set('store.media_cdn_url', $publicUrl);
        }

        if ($settings->use_for_uploads) {
            Config::set('filesystems.disks.public', array_merge($s3, [
                // Filament/MediaUrl disk('public') çağrıları R2'ye gider
                'root' => '',
            ]));
            Config::set('filesystems.default', 's3');
        }

        return true;
    }
}
