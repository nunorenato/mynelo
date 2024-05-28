<?php

namespace App\Models\Magento;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerEntity extends Model
{
    protected $connection = 'magento';
    protected $table = 'customer_entity';
    protected $primaryKey = 'entity_id';

    public function newQuery(): Builder{
        return parent::newQuery()->where('store_id',  6);
    }

    public function orders():HasMany
    {
        return $this->hasMany(SalesOrder::class, 'customer_id');
    }
}
