<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attribute extends Model
{
    protected $fillable = [
        'name',
    ];

    protected function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }
}
