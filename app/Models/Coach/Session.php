<?php

namespace App\Models\Coach;

use App\Helpers\SessionValues;
use App\Imports\SessionImport;
use App\Models\Boat;
use App\Models\BoatRegistration;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class Session extends Model implements SessionValues
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

    /**
     * Calculates the duration in seconds
     *
     * @return Attribute
     */
    protected function duration():Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => ($attributes['end_time']-$attributes['start_time']),
        );
    }

    public function sessionData():HasMany{
        return $this->hasMany(SessionData::class, 'trainid');
    }

    public function laps():HasMany
    {
        return $this->hasMany(SessionLap::class, 'trainid');
    }

    public function boat():BelongsTo{
        return $this->belongsTo(BoatRegistration::class, 'boatid');
    }

    public function user():BelongsTo{
        return $this->belongsTo(User::class, 'athleteid', 'athlete_id');
    }

    /**
     * Import session data from file
     *
     * @param string $filename
     * @return void
     */
    public function importData(string $filename){

        Log::info("Session import from file {$filename}");

        Excel::import(new SessionImport($this), $filename);

        \Storage::disk('local')->delete('coach-tmp/'.basename($filename));
    }

    /**
     * Import session laps from file
     *
     * @param string $filename
     * @return void
     */
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

    /**
     * Recalcs the session statistics
     *
     * @return void
     */
    public function updateStats():void{

        Log::info("Calculating stats for session {$this->id}");

        $stats = $this->sessionData()
            ->selectRaw('IFNULL(SUM( speed ), 0) AS distance,
                                IFNULL(AVG( speed ),0) AS avg_speed,
                                IFNULL(MAX( speed ),0) AS max_speed,
                                IFNULL(UNIX_TIMESTAMP(MIN(tagtime)),0) AS start_time,
                                IFNULL(UNIX_TIMESTAMP(MAX(tagtime)),0) AS end_time,
                                IFNULL(MAX( spm ),0) AS max_spm,
                                IFNULL(AVG( spm ),0) AS avg_spm,
                                IFNULL(AVG( heart),0) as avg_heart,
                                IFNULL(MAX( heart),0) as max_heart,
                                IFNULL(AVG( dps),0) as avg_dps,
                                1 as gpslat')
            ->first();

        $this->update($stats->toArray());
    }

    /**
     * Creates a new SessionSelection instance from timestamps
     *
     * @param int $startTime
     * @param int $endTime
     * @return SessionSelection
     */
    public function selectionBuilder(int $startTime, int $endTime):SessionSelection{

        $ss = SessionSelection::find($this->id);
        $ss->crop($startTime, $endTime);

        return $ss;
    }

    /**
     * Creates a new SessionSelection instance from distance points
     *
     * @param int $startPoint
     * @param int $endPoint
     * @return SessionSelection
     */
    public function selectionDistanceBuilder(int $startPoint, int $endPoint):SessionSelection{

        $ss = SessionSelection::find($this->id);
        $ss->crop($this->distanceToTime($startPoint), $this->distanceToTime($endPoint));

        return $ss;
    }

    public function relativeTimestamp2Absolute(int $timestamp){
        return $this->start_time + $timestamp;
    }

    public function absoluteTimestamp2relative(int $timestamp){
        return $timestamp - $this->start_time;
    }

    public function distanceToTime(int $distance):int{

        $total = 0;

        foreach($this->sessionData as $row){
            $total += max($row->speed, 0); // deal with negative values
            if($total >= $distance){
                break;
            }
        }
        return $row->tagtimestamp;
    }

    public function timeToDistance(int $time):int{
        return $this->sessionData()->where('tagtimestamp', '<=', $time)->sum('speed');
    }

    public function scopeProcessed(Builder $query):void{
        $query->where('gpslat', 1);
    }

    public function getSpeed()
    {
        return $this->avg_speed??0;
    }

    public function getAvgSpeed()
    {
        return $this->avg_speed??0;
    }

    public function getMaxSpeed()
    {
        return $this->max_speed??0;
    }

    public function getDistance()
    {
        return $this->distance??0;
    }

    public function getDuration()
    {
        return $this->duration??0;
    }

    public function getAvgSpm()
    {
        return $this->avg_spm??0;
    }

    public function getMaxSpm()
    {
        return $this->max_spm??0;
    }

    public function getAvgHeart()
    {
        return $this->avg_heart??0;
    }

    public function getMaxHeart()
    {
        return $this->max_heart??0;
    }

    public function getDps()
    {
        return $this->dps??0;
    }

    public function getAvgDps()
    {
        return $this->avg_dps??0;
    }

    public function getMaxDps()
    {
        return $this->max_dps??0;
    }
}

