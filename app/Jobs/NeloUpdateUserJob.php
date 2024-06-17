<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\NeloApiClient;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NeloUpdateUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly User $user)
    {
    }

    public function handle(): void
    {
        $nelo = new NeloApiClient();
        $nelo->updateOwner($this->user);
    }
}
