<?php

namespace App\Models\Coach;

use App\Helpers\SessionValues;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionData extends Model implements SessionValues
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

    protected $casts = [
        'tagtime' => 'immutable_datetime',
    ];


    public function session():BelongsTo
    {
        return $this->belongsTo(Session::class, 'trainid');
    }

    public function relativeTimestamp():Attribute{
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['tagtimestamp'] - $this->session->start_time,
        );
    }

    public function getSpeed()
    {
        return $this->speed;
    }

    public function getAvgSpeed()
    {
        return $this->speed;
    }

    public function getMaxSpeed()
    {
        return $this->speed;
    }

    public function getDistance()
    {
        return $this->speed;
    }

    public function getDuration()
    {
        return 1;
    }

    public function getAvgSpm()
    {
        return $this->spm;
    }

    public function getMaxSpm()
    {
        return $this->spm;
    }

    public function getAvgHeart()
    {
        return $this->heart;
    }

    public function getMaxHeart()
    {
        return $this->heart;
    }

    public function getDps()
    {
        return $this->dps;
    }

    public function getAvgDps()
    {
        return $this->dps;
    }

    public function getMaxDps()
    {
        return $this->dps;
    }

}
