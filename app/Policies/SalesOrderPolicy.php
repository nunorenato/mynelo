<?php

namespace App\Policies;

use App\Models\Magento\SalesOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesOrderPolicy
{
    use HandlesAuthorization;

    public function view(User $user, SalesOrder $order): bool
    {
        //dd($user->paddleLabCustomer()->entity_id);
        //dump($order->customer_id);
        return (!empty($user->paddleLabCustomer)?$user->paddleLabCustomer->entity_id:null) == $order->customer_id || $user->email == $order->customer_email || $user->isAdmin();
    }
}
