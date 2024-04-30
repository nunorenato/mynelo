<?php

namespace App\Console;

use App\Http\Controllers\DealerController;
use App\Models\Product;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
      /*  $schedule->call(function(){
            $dc = new DealerController();
            $dc->sync();
        })
            ->name('Sync Dealers')
            ->weekly();*/

        $schedule->call(function(){
            Product::updateAll();
        })->name('Sync products')
            ->weekly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
