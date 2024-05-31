<?php

namespace App\Models\Magento;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderItem extends Model
{
    protected $connection = 'magento';
    protected $table = 'sales_order_item';
    protected $primaryKey = 'item_id';

    protected $casts = [
      'product_options' => 'json',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }

    public function product():BelongsTo
    {
        return $this->belongsTo(Product::class, 'sku', 'external_id');
    }
}
