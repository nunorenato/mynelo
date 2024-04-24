<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Content extends Model
{
    protected $fillable = [
        'path',
        'title',
        'content',
    ];

    protected function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }
}
