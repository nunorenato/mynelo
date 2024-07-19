<?php

namespace App\Models\Magento;

use App\Enums\MagentoStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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

    public static function allOrders(User $user):Collection{

        $nonCustomer = \App\Models\Magento\PaddleLabSalesOrder::whereNull('customer_id')->where('customer_email', $user->email);

        $customer = $user->paddleLabCustomer;
        if(!empty($customer)){
            // search for order done by the same email before beign a customer
            return $customer->orders()->union($nonCustomer)->latest()->get();
        }
        else{
            return $nonCustomer->latest()->get();
        }

    }

}
