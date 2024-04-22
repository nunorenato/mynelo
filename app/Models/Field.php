<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'column',
    ];

}
