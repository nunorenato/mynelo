<?php

namespace App\Models\Coach;

use App\Imports\SessionImport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class Session extends Model
{
    const CREATED_AT = 'createdon';
    const UPDATED_AT = 'editedon';


    protected $connection = 'coach';
    protected $table = 'sierraw_train';

    protected $fillable = [
        'athleteid',
        'labelid',
        'boatid',
        'details',
        'gpslat',
        'gpslng',
        'windspeed',
        'winddirectionid',
        'editedon',
        'deletedon',
        'createdon',
        'estado_treino',
        'avg_speed',
        'max_speed',
        'avg_spm',
        'max_spm',
        'start_time',
        'end_time',
        'distance',
        'avg_dps',
        'avg_heart',
        'max_heart'
    ];

    public function sessionData():HasMany{
        return $this->hasMany(SessionData::class, 'trainid');
    }

    public function laps():HasMany
    {
        return $this->hasMany(SessionLap::class, 'trainid');
    }

    public function importData(string $filename){

        Log::info("Session import from file {$filename}");

        $this->sessionData()->delete();

        Excel::import(new SessionImport($this), $filename);

        \Storage::disk('local')->delete('coach-tmp/'.basename($filename));
    }

    public function importLaps(string $filename){

        Log::info("Import laps: $filename");

        $csv = file_get_contents($filename); //Storage::get($this->filename);
        $lines = explode("\n", $csv);

        foreach ($lines as $line) {

            $points = explode(';', $line);
            if(count($points) != 2)
                continue;

            $start = Carbon::createFromTimestampMs($points[0]);
            $end = Carbon::createFromTimestampMs($points[1]);

            $lap = SessionLap::firstOrCreate([
                'trainid' => $this->id,
                'tagtime' => $start->toDateTimeString(),
                'endtime' => $end->toDateTimeString(),
                'decima_segundo1' => $start->milli,
                'decima_segundo2' => $end->milli,
            ]);

            $stats = $this->sessionData()
                ->whereBetween('tagtime', [$start, $end])
                ->selectRaw('AVG(speed) as stat_avgSpeed, MAX(speed) as stat_maxSpeed, MAX(spm) as stat_maxSPM, SUM(speed) as stat_distance, AVG(spm) as stat_avgSPM, 1 as calculado')
                ->first();

            $lap->update($stats->toArray());

            \Storage::disk('local')->delete('coach-tmp/'.basename($filename));

        }
    }
}

