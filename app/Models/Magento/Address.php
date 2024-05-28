<?php

namespace App\Models\Magento;

use App\Models\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $connection = 'magento';
    protected $table = 'sales_order_address';
    protected $primaryKey = 'entity_id';

    public function country():BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'code');
    }
}
