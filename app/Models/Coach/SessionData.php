<?php

namespace App\Models\Coach;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionData extends Model
{
    public $timestamps = false;

    protected $connection = 'coach';
    protected $table = 'sierraw_traindata';

    protected $fillable = [
        'trainid',
        'tagtime',
        'decima_segundo',
        'speed',
        'spm',
        'pitch',
        'roll',
        'heading',
        'gpsx',
        'gpxy',
        'gpsz',
        'dps',
        'accelX',
        'accelY',
        'accelZ',
        'raw',
        'accu',
        'head2',
        'heart',
        'app_spm'
    ];

    public function session():BelongsTo
    {
        return $this->belongsTo(Session::class, 'trainid');
    }

}
