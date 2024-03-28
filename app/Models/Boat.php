<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Boat extends Model
{
    protected $fillable = [
        'model',
        'finished_at',
        'finished_weight',
        'product_id',
        'ideal_weight',
        'external_id',
    ];

    protected $casts = [
        'finished_at' => 'date',
    ];

    public function model():BelongsTo{
        return $this->belongsTo(Product::class);
    }
}
