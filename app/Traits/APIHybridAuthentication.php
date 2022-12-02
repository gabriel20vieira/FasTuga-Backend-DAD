<?php

namespace App\Traits;

use App\Models\User;

trait APIHybridAuthentication
{

    /**
     * Provides the user for policies
     *
     * @param User|null $user
     * @return User
     */
    private function ifAuthenticated(?User $user)
    {
        return $user ? $user : auth('api')->user() ?? (new User());
    }

    /**
     * Checks if user is authenticated
     *
     * @return boolean
     */
    private function isAuthenticated()
    {
        return auth('api')->check();
    }
}
