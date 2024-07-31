<?php

namespace App\Jobs;

use App\Imports\SessionImport;
use App\Models\Coach\Session;
use App\Models\Coach\SessionLap;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CoachSessionUploadLapJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Session $session, private readonly string $filename)
    {
    }

    public function handle(): void
    {
        Log::info("Job processing lap file {$this->filename}");

        $this->session->importLaps($this->filename);

    }
}
