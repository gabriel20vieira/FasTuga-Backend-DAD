<?php

namespace App\Models;

use App\Models\Types\OrderItemStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'order_local_number',
        'product_id',
        'status',
        'price',
        'preparation_by',
        'notes'
    ];

    /**
     * Identification attribute
     *
     * @return string
     */
    public function getIdentificationAttribute()
    {
        return $this->order->ticket_number . "-" . $this->order_local_number;
    }

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
     * Product relationship
     *
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Order relationship
     *
     * @return BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Prepared by relationship
     *
     * @return HasOne
     */
    public function preparated()
    {
        return $this->hasOne(User::class, 'id', 'preparation_by');
    }

    /**
     * Scope a given status
     *
     * @param Builder $query
     * @param OrderItemStatus $status
     * @return Builder
     */
    public function scopeWhereStatus($query, OrderItemStatus $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Ready scope
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeReady($query)
    {
        return $query->whereStatus(OrderItemStatus::READY);
    }

    /**
     * Ready scope
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopePreparing($query)
    {
        return $query->whereStatus(OrderItemStatus::PREPARING);
    }

    /**
     * Ready scope
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeWaiting($query)
    {
        return $query->whereStatus(OrderItemStatus::WAITING);
    }
}
