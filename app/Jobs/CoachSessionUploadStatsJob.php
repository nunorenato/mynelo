<?php

namespace App\Jobs;

use App\Models\Coach\Session;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CoachSessionUploadStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Session $session)
    {
    }

    public function handle(): void
    {
        Log::info("Calculating stats for session {$this->session->id}");

        $stats = $this->session->sessionData()
            ->selectRaw('SUM( speed ) AS distance,
                                AVG( speed ) AS avg_speed,
                                MAX( speed ) AS max_speed,
                                UNIX_TIMESTAMP(MIN(tagtime)) AS start_time,
                                UNIX_TIMESTAMP(MAX(tagtime)) AS end_time,
                                MAX( spm ) AS max_spm,
                                AVG( spm ) AS avg_spm,
                                AVG( heart) as avg_heart,
                                MAX( heart) as max_heart')
            ->first();

        $this->session->update($stats->toArray());
    }
}
