<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Types\UserType;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'photo_url',
        'blocked'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Customer associated with user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    /**
     * Associated customer orders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->customer->orders();
    }

    /**
     * Prepared relationship
     *
     * @return void
     */
    public function prepared()
    {
        return $this->hasMany(OrderItem::class, 'preparation_by', 'id');
    }

    /**
     * Get user latest payment
     *
     * @return void
     */
    public function getPaymentAttribute()
    {
        return $this->orders()
            ->getQuery()
            ->latest()
            ->whereBetween('created_at', [now()->subMinutes(env('PAYMENT_TIME', 5)), now()])
            ->first();
    }

    /**
     * Get user valid payments
     *
     * @return void
     */
    public function getPaymentsAttribute()
    {
        return $this->orders()
            ->getQuery()
            ->latest()
            ->whereBetween('created_at', [now()->subMinutes(env('PAYMENT_TIME', 5)), now()])
            ->get();
    }

    /**
     * Type selection
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $type
     * @return void
     */
    public function scopeOfType(Builder $builder, mixed $type)
    {
        if ($type && in_array($type, UserType::toArray())) {
            $builder = $builder->where('type', $type);
        }
    }

    /**
     * Checks user type
     *
     * @param UserType $type
     * @return boolean
     */
    public function isOfType(UserType $type)
    {
        return ($this->type ?? null) == $type->value;
    }

    /**
     * Checks if user is manager
     *
     * @return boolean
     */
    public function isManager()
    {
        return $this->isOfType(UserType::MANAGER);
    }

    /**
     * Checks if user is Chef
     *
     * @return boolean
     */
    public function isChef()
    {
        return $this->isOfType(UserType::CHEF);
    }

    /**
     * Checks if user is customer
     *
     * @return boolean
     */
    public function isCustomer()
    {
        return $this->isOfType(UserType::CUSTOMER);
    }

    /**
     * Checks if user is delivery guy
     *
     * @return boolean
     */
    public function isDelivery()
    {
        return $this->isOfType(UserType::DELIVERY);
    }

    /**
     * Allows any user that is not authenticated
     *
     * @return boolean
     */
    public function isAnonymous()
    {
        return auth('api')->user() == null;
    }

    /**
     * Allows anyone
     *
     * @return boolean
     */
    public function isAny()
    {
        return true;
    }

    /**
     * Blocks user
     *
     * @return void
     */
    public function block()
    {
        $this->blocked = 1;
        $this->save();
    }

    /**
     * Unblock user
     *
     * @return void
     */
    public function unblock()
    {
        $this->blocked = 0;
        $this->save();
    }

    /**
     * Checks if user is blocked
     *
     * @return boolean
     */
    public function isBlocked()
    {
        return  $this->blocked == 1;
    }
}
