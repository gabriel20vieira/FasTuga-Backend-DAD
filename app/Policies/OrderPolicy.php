<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use App\Traits\APIHybridAuthentication;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization, APIHybridAuthentication;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(?User $user)
    {
        return $this->isAuthenticated() && !$this->ifAuthenticated($user)->isChef();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(?User $user, Order $order)
    {
        return $this->ifAuthenticated($user)->isManager()
            || ($order->customer ? ($order->customer->user->id ?? null) == $this->ifAuthenticated($user)->id : false);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(?User $user)
    {
        return $this->ifAuthenticated($user)->isCustomer()
            || $this->ifAuthenticated($user)->isAnonymous();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(?User $user, Order $order)
    {
        return $this->ifAuthenticated($user)->isDelivery() || $this->ifAuthenticated($user)->isManager();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(?User $user, Order $order)
    {
        return $this->ifAuthenticated($user)->isManager();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(?User $user, Order $order)
    {
        return $this->ifAuthenticated($user)->isManager();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(?User $user, Order $order)
    {
        return $this->ifAuthenticated($user)->isManager();
    }
}
