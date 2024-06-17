<?php

namespace App\Jobs;

use App\Models\BoatRegistration;
use App\Models\User;
use App\Services\NeloApiClient;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NeloStoreBoatRegistrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly BoatRegistration $boatRegistration)
    {

    }

    public function handle(): void
    {
        $nelo = new NeloApiClient();
        $nelo->storeRegistration($this->boatRegistration);
    }
}
