<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoatRegistration extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'boat_id',
        'user_id',
        'seat_id',
        'seat_position',
        'seat_height',
        'footrest_id',
        'rudder_id',
        'paddle',
        'paddle_length',
        'status',
        'seller_id',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    public function boat():BelongsTo
    {
        return $this->belongsTo(Boat::class);
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller():BelongsTo
    {
        return $this->belongsTo(Dealer::class, 'seller_id');
    }

}
