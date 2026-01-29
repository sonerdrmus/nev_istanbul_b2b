<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // TCMB kurlarını her saat otomatik güncelle (butona basmaya gerek yok)
        $schedule->command('currency:update-rates')
            ->hourly()
            ->timezone('Europe/Istanbul')
            ->withoutOverlapping(55);
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
