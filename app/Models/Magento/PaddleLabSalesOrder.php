<?php

namespace App\Models\Magento;

use Illuminate\Database\Eloquent\Builder;

class PaddleLabSalesOrder extends SalesOrder
{
    public function newQuery(): Builder{
        return parent::newQuery()->where('store_id', '=', 6);
    }
}
