<?php

namespace App\Models\Coach;

use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    public $timestamps = false;

    protected $connection = 'coach';
    protected $table = 'sierraw_athlete';
}
