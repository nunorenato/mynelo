<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    public const BOAT = 1;

    protected $fillable = [
        'name',
    ];
}