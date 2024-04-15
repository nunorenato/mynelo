<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Boat extends Model
{
    protected $fillable = [
        'model',
        'finished_at',
        'finished_weight',
        'product_id',
        'ideal_weight',
        'external_id',
        'seller',
    ];

    protected $casts = [
        'finished_at' => 'date',
    ];

    public function model():BelongsTo{
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function painter():BelongsTo{
        return $this->belongsTo(Worker::class, 'painter_id');
    }
    public function layuper():BelongsTo{
        return $this->belongsTo(Worker::class, 'layuper_id');
    }
    public function evaluator():BelongsTo{
        return $this->belongsTo(Worker::class, 'evaluator_id');
    }

    public function images():BelongsToMany{
        return $this->belongsToMany(Image::class);
    }

    public function products():BelongsToMany{
        return $this->belongsToMany(Product::class);
    }
}
