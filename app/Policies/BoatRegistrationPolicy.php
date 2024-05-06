<?php

namespace App\Policies;

use App\Models\BoatRegistration;
use App\Models\User;
use App\Models\Boat;

class BoatRegistrationPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user, BoatRegistration $boatRegistration):bool{
        return $boatRegistration->user_id == $user->id || $user->isAdmin();
    }
}
