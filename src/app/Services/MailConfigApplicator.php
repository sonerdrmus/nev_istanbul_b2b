<?php

namespace App\Services;

use App\Models\MailSetting;
use Illuminate\Support\Facades\Config;
use Throwable;

class MailConfigApplicator
{
    public function apply(?MailSetting $settings = null): bool
    {
        if (! MailSetting::tableReady()) {
            return false;
        }

        $settings ??= MailSetting::query()->first();
        if (! $settings || ! $settings->isConfigured()) {
            return false;
        }

        $mailer = $settings->mailer ?: 'smtp';
        $scheme = filled($settings->scheme) ? strtolower((string) $settings->scheme) : null;
        if ($scheme === '') {
            $scheme = null;
        }

        Config::set('mail.default', $mailer);
        Config::set('mail.from.address', $settings->from_address);
        Config::set('mail.from.name', $settings->from_name ?: config('app.name', 'Nevistanbul'));

        if ($mailer === 'smtp') {
            Config::set('mail.mailers.smtp.transport', 'smtp');
            Config::set('mail.mailers.smtp.host', $settings->host);
            Config::set('mail.mailers.smtp.port', (int) $settings->port);
            Config::set('mail.mailers.smtp.scheme', $scheme);
            Config::set('mail.mailers.smtp.username', $settings->username);
            Config::set('mail.mailers.smtp.password', $settings->password);
        }

        $this->purgeMailers([$mailer, 'smtp']);

        return true;
    }

    /**
     * @param  list<string>  $mailers
     */
    private function purgeMailers(array $mailers): void
    {
        try {
            $manager = app('mail.manager');
        } catch (Throwable) {
            return;
        }

        foreach (array_unique($mailers) as $mailer) {
            try {
                $manager->purge($mailer);
            } catch (Throwable) {
                // ignore
            }
        }
    }
}
