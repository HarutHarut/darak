<?php

namespace App\Console;

use App\Console\Commands\SendEmail;
use App\Console\Commands\Settings;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SendEmail::class,
        Settings::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('command:email')->everyMinute();
        $schedule->command('command:currency_settings')->hourly();
//         $schedule->command('send:currency_settings')->everyMinute();
        $schedule->command('command:invoice')->monthlyOn(2);
        $schedule->command('command:change_order_status')->everyMinute();
        $schedule->command('command:transaction')->everyMinute();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
