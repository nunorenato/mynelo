<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends Model
{
    protected $table = 'people';
    protected $fillable = [
        'name',
        'external_id',
        'image_id',
    ];

    protected function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    protected function personType(): BelongsTo
    {
        return $this->belongsTo(PersonType::class);
    }

    public function photo():BelongsTo
    {
        return $this->BelongsTo(Image::class, 'image_id');
    }
}
