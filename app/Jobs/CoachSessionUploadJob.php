<?php

namespace App\Jobs;

use App\Imports\SessionImport;
use App\Models\Coach\Session;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use function Laravel\Prompts\error;

class CoachSessionUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Session $session, private readonly string $filename)
    {
    }

    public function handle(): void
    {
        Log::debug("Job processing file {$this->filename}");

        Excel::import(new SessionImport($this->session), $this->filename);
    }
}
