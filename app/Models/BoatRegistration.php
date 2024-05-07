<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'footrest_position',
        'rudder_id',
        'paddle',
        'paddle_length',
        'status',
        'seller',
        'hash',
        'voucher',
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

    /*public function images():BelongsToMany
    {
        return $this->belongsToMany(Image::class);
    }*/

    public function seat():BelongsTo{
        return $this->belongsTo(Product::class, 'seat_id');
    }
    public function footrest():BelongsTo{
        return $this->belongsTo(Product::class, 'footrest_id');
    }
    public function rudder():BelongsTo{
        return $this->belongsTo(Product::class, 'rudder_id');
    }

}
