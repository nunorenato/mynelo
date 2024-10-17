<?php

namespace App\Helpers;

use App\Helpers\SessionValues;
use App\Models\User;

class AthleteTotals implements SessionValues
{
    private float $totalDistance = 0;
    private float $totalDuration = 0;
    public int $nSessions = 0;
    private float $avgSpeed = 0;
    private float $avgDps = 0;

    public function __construct(private readonly User $user){
        $sessions = $user->coachSessions();
        $this->nSessions = $sessions->count();

        if ($this->nSessions > 0) {
            //$this->totalDistance = $sessions->sum('distance');
            //$this->avgSpeed = $sessions->avg('avg_speed');
            $totals = $sessions->selectRaw('SUM(distance) as distance,
                                AVG(avg_speed) as avg_speed,
                                AVG(avg_dps) as avg_dps,
                                SUM(end_time - start_time) as duration_sum,
                                0 as start_time, 0 as end_time')
                ->first();
            $this->totalDistance = $totals->distance??0;
            $this->totalDuration = ($totals->duration_sum??0)/3600; // in hours
            $this->avgSpeed = $totals->avg_speed??0;
            $this->avgDps = $totals->avg_dps??0;
        }
    }

    public function getSpeed()
    {
        return $this->avgSpeed;
    }

    public function getAvgSpeed()
    {
        return $this->avgSpeed;
    }

    public function getMaxSpeed()
    {
        return $this->avgSpeed;
    }

    public function getDistance()
    {
        return $this->totalDistance;
    }

    public function getDuration()
    {
        return $this->totalDuration;
    }

    public function getAvgSpm()
    {
        return 0;
    }

    public function getMaxSpm()
    {
        return 0;
    }

    public function getAvgHeart()
    {
        return 0;
    }

    public function getMaxHeart()
    {
        return 0;
    }

    public function getDps()
    {
        return $this->avgDps;
    }

    public function getAvgDps()
    {
        return $this->avgDps;
    }

    public function getMaxDps()
    {
        return $this->avgDps;
    }
}
