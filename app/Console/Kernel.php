<?php

namespace App\Console;

use App\Http\Controllers\DealerController;
use App\Jobs\BoatSyncJob;
use App\Models\Boat;
use App\Models\Product;
use App\Models\User;
use App\Services\MagentoApiClient;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

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

        $schedule->call(function(){
            foreach (Boat::whereNull('finished_at')->get() as $boat){
                BoatSyncJob::dispatch($boat, $boat->external_id);
            }
        })->name('update unfinished')
            ->dailyAt('02:00');

        $schedule->command('model:prune')->daily();

        $schedule->call(function(){
            foreach(Storage::files('livewire-tmp') as $file){
                //dump($file);
                $time = Storage::lastModified($file);
                $fileModifiedDateTime = Carbon::parse($time);
                if(now()->subDay()->gt($fileModifiedDateTime)){
                    dump("delete $file");
                    Storage::delete($file);
                }
            }
        })->dailyAt('03:00')->name('Delete temp uploads');

        $schedule->call(function (){
            $users = User::whereJsonContains('extras->coupon_used', false)->get();
            dump(count($users));
            $magento = new MagentoApiClient();
            foreach ($users as $user){
                $coupon = $magento->searchCouponByCode($user->extras['coupon']);
                if(!empty($coupon) && $coupon->times_used > 0){
                    //$user->extras['coupon_used'] = true;
                    $user->update(['extras->coupon_used' => true]);
                    //$user->save();
                }
                //dd($coupon);
            }
        })->dailyAt('02:00')->name('Update used coupon');
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
