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

        $csv = file_get_contents($this->filename); //Storage::get($this->filename);
        $lines = explode("\n", $csv);

        foreach ($lines as $line) {

            $points = explode(';', $line);
            if(count($points) != 2)
                continue;

            $start = Carbon::createFromTimestampMs($points[0]);
            $end = Carbon::createFromTimestampMs($points[1]);

            $lap = SessionLap::firstOrCreate([
                'trainid' => $this->session->id,
                'tagtime' => $start->toDateTimeString(),
                'endtime' => $end->toDateTimeString(),
                'decima_segundo1' => $start->milli,
                'decima_segundo2' => $end->milli,
            ]);

            $stats = $this->session->sessionData()
                ->whereBetween('tagtime', [$start, $end])
                ->selectRaw('AVG(speed) as stat_avgSpeed, MAX(speed) as stat_maxSpeed, MAX(spm) as stat_maxSPM, SUM(speed) as stat_distance, AVG(spm) as stat_avgSPM, 1 as calculado')
                ->first();

            $lap->update($stats->toArray());

        }

    }
}
