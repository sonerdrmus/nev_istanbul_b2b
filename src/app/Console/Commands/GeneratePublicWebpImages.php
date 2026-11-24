<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GeneratePublicWebpImages extends Command
{
    protected $signature = 'media:generate-webp {--dir= : storage/app/public alt dizini} {--quality=80}';

    protected $description = 'PNG/JPEG dosyaları için yanına WebP kopya üretir (storefront picture fallback).';

    public function handle(): int
    {
        if (! function_exists('imagewebp')) {
            $this->error('PHP GD WebP desteği yok.');

            return self::FAILURE;
        }

        $relative = trim((string) ($this->option('dir') ?: ''), '/');
        $root = storage_path('app/public'.($relative !== '' ? '/'.$relative : ''));
        if (! is_dir($root)) {
            $this->error('Dizin bulunamadı: '.$root);

            return self::FAILURE;
        }

        $quality = max(40, min(95, (int) $this->option('quality')));
        $count = 0;

        foreach (File::allFiles($root) as $file) {
            $ext = strtolower($file->getExtension());
            if (! in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
                continue;
            }
            $source = $file->getPathname();
            $target = preg_replace('/\.(jpe?g|png)$/i', '.webp', $source);
            if (! is_string($target) || is_file($target)) {
                continue;
            }

            $image = match ($ext) {
                'png' => @imagecreatefrompng($source),
                default => @imagecreatefromjpeg($source),
            };
            if ($image === false) {
                continue;
            }

            if ($ext === 'png') {
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
            }

            if (@imagewebp($image, $target, $quality)) {
                $count++;
                $this->line('OK '.$target);
            }
            imagedestroy($image);
        }

        $this->info("Üretilen WebP: {$count}");

        return self::SUCCESS;
    }
}
