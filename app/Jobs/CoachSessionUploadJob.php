<?php

namespace App\Jobs;

use App\Models\Coach\Session;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CoachSessionUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Session $session, private readonly string $filename)
    {
    }

    public function handle(): void
    {
        Log::info("Job processing file {$this->filename}");

        $this->session->importData($this->filename);
    }
}
