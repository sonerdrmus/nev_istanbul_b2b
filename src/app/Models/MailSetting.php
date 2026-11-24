<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class MailSetting extends Model
{
    protected $fillable = [
        'enabled',
        'mailer',
        'host',
        'port',
        'scheme',
        'username',
        'password',
        'from_address',
        'from_name',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'port' => 'integer',
            'password' => 'encrypted',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'enabled' => false,
            'mailer' => 'smtp',
            'port' => 587,
            'scheme' => 'tls',
            'from_name' => config('app.name', 'Nevistanbul'),
        ]);
    }

    public static function tableReady(): bool
    {
        try {
            return Schema::hasTable('mail_settings');
        } catch (\Throwable) {
            return false;
        }
    }

    public function isConfigured(): bool
    {
        if (! $this->enabled) {
            return false;
        }

        if (($this->mailer ?: 'smtp') === 'log') {
            return true;
        }

        return filled($this->host)
            && filled($this->from_address)
            && (int) $this->port > 0;
    }
}
