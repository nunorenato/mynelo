<?php

namespace App\Models\Coach;

use App\Models\Coach\Session;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionSelection extends Session
{

    public function crop(int $startTime, int $endTime){
        $this->start_time = $startTime;
        $this->end_time = $endTime;

        $stats = $this->sessionData()
            ->selectRaw('SUM( speed ) AS distance,
                                AVG( speed ) AS avg_speed,
                                MAX( speed ) AS max_speed,
                                MAX( spm ) AS max_spm,
                                AVG( spm ) AS avg_spm,
                                AVG( heart) as avg_heart,
                                MAX( heart) as max_heart,
                                AVG(dps) as avg_dps')
            ->get()->toArray()[0];

        $this->fill($stats);
    }

    public function sessionData():HasMany{
        return parent::sessionData()->whereBetween('tagtimestamp', [$this->start_time, $this->end_time]);
    }

    public function save(array $options = [])
    {
        return $this;
    }

}
