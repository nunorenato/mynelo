<?php

namespace App\Policies;

use App\Models\Coach\Session;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoachSessionPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Session $session): bool
    {

        if(empty($session->user)){
            return false;
        }

        return $session->user->is($user)
            || $user->isAdmin();
    }


}
