<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'image',
        'external_id',
        'product_type_id',
        'discipline_id',
        'attributes',
        'attributes->hex',
        'description',
    ];

    protected $casts = [
        'attributes' => 'json',
        'external_id' => 'int'
    ];


    public function type():BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function discipline():BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    public function image():BelongsTo{
        return $this->belongsTo(Image::class);
    }

    public function options():BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_options','main_product_id', 'sub_product_id')
            ->withPivot(['attribute_id', 'standard']);
    }
}
