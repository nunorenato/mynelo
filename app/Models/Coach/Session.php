<?php

namespace App\Models\Coach;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}

