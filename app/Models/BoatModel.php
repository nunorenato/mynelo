<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class BoatModel extends \App\Models\Product
{
    protected $table = 'products';

    public function newQuery(): Builder{
        return parent::newQuery()->where('product_type_id', '=', \App\Enums\ProductTypeEnum::Boat);
    }
}
