<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\MagentoApiClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MagentoCouponJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function handle(): void
    {
        $magento = new MagentoApiClient();
        $coupon = $magento->generateCoupon(config('nelo.magento.coupon_rule'));

        if(empty($coupon)){
            if($this->attempts() < 4){
                $this->release();
            }
            else{
                Log::error('Could not get coupon', $this->user->toArray());
            }
        }
        else{
            $this->user->extras = [
                'coupon' => $coupon,
                'coupon_used' => false,
            ];
            $this->user->save();
        }

    }
}
