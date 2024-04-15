<?php

namespace App\Models;

use App\Models\Product;

class BoatModel extends Product
{
    public function newQuery(){
        return parent::newQuery()->where('E_ENT_ID', '=', \App\Enums\ProductTypeEnum::Boat);
    }
}
