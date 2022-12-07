<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'phone',
        'points',
        'nif',
        'default_payment_type',
        'default_payment_reference',
        'custom'
    ];

    /**
     * User relationship
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Orders relationship
     *
     * @return HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Payment type getter mutator
     *
     * @return string
     */
    public function getDefaultPaymentTypeAttribute($value)
    {
        return strtoupper($value);
    }

    /**
     * Payment type setter mutator
     *
     * @param string $value
     * @return void
     */
    public function setDefaultPaymentTypeAttribute($value)
    {
        $this->attributes['default_payment_type'] = strtoupper($value);
    }
}
