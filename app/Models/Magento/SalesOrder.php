<?php

namespace App\Models\Magento;

use App\Enums\MagentoStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    protected $connection = 'magento';
    protected $table = 'sales_order';
    protected $primaryKey = 'entity_id';

    protected $casts = [
        'status' => MagentoStatusEnum::class,
    ];

    public function items():HasMany{
        return $this->hasMany(SalesOrderItem::class, 'order_id');
    }

    public function addresses():HasMany
    {
        return $this->hasMany(Address::class, 'parent_id');
    }

}
