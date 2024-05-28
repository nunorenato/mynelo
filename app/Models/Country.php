<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    /*
     * Needed because there is a relationship with a Magento table (Address)
     * This forces to use here the correct connection
     */
    protected $connection = 'mysql';
}
