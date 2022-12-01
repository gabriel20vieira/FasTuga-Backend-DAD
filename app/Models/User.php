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
        return $this->type == $type->value;
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
        return auth()->user() == null;
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
}
