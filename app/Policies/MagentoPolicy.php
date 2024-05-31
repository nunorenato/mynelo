<?php

namespace App\Policies;

use App\Models\Magento\SalesOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MagentoPolicy
{
    use HandlesAuthorization;

    public function viewOrder(User $user, SalesOrder $order): bool
    {
        return /*$user->paddleLabCustomer()->entity_id == $order->customer_id || $user->email == $order->customer_email ||*/ $user->isAdmin();
    }
}
