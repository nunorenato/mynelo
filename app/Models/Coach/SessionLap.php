<?php

namespace App\Models\Coach;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SessionLap extends Model
{
    public $timestamps = false;

    protected $connection = 'coach';
    protected $table = 'sierraw_trainlap';

    protected $fillable = [
        'trainid',
        'tagtime',
        'decima_segundo1',
        'endtime',
        'decima_segundo2',
        'description',
        'estado_lap',
        'tipo_lap',
        'duration_distancia',
        'diatncia_distancia',
        'stat_distance',
        'stat_avgSpeed',
        'stat_maxSpeed',
        'stat_maxSPM',
        'stat_avgSPM',
        'calculado',
        'stat_avgHeart',
        'stat_maxHeart'
    ];

    public function session():BelongsTo{
        return $this->belongsTo(Session::class, 'trainid');
    }

}
