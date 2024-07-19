<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    protected $fillable = [
        'name',
        'sort',
        'rules',
    ];

    protected $casts = [
        'rules' => 'json',
    ];
}
