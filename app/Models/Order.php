<?php

namespace App\Models;

use App\Models\Types\PaymentType;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Types\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'status',
        'customer_id',
        'total_price',
        'total_paid',
        'total_paid_with_points',
        'points_gained',
        'points_used_to_pay',
        'payment_type',
        'payment_reference',
        'date',
        'delivered_by'
    ];

    /**
     * Customer relationship
     *
     * @return BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * OrderItems relationship
     *
     * @return HasMany
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Delivered by relationship
     *
     * @return HasOne
     */
    public function delivered()
    {
        return $this->hasOne(User::class, 'id', 'delivered_by');
    }

    /**
     * Scope a given status
     *
     * @param Builder $query
     * @param OrderStatus $status
     * @return Builder
     */
    public function scopeWhereStatus($query, OrderStatus $status)
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
        return $query->whereStatus(OrderStatus::READY);
    }

    /**
     * Ready scope
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopePreparing($query)
    {
        return $query->whereStatus(OrderStatus::PREPARING);
    }

    /**
     * Ready scope
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeDelivered($query)
    {
        return $query->whereStatus(OrderStatus::DELIVERED);
    }

    /**
     * Ready scope
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeCanceled($query)
    {
        return $query->whereStatus(OrderStatus::CANCELED);
    }

    /**
     * Payment type getter mutator
     *
     * @return string
     */
    public function getPaymentTypeAttribute($value)
    {
        return strtoupper($value);
    }

    /**
     * Payment type setter mutator
     *
     * @param string $value
     * @return void
     */
    public function setPaymentTypeAttribute($value)
    {
        $this->attributes['payment_type'] = strtoupper($value);
    }

    /**
     * Retrieves the next ticket number
     *
     * @return int
     */
    public static function getNextTicket()
    {
        $last_ticket = Order::latest()->first()->ticket_number;
        $ticket_number = $last_ticket + 1 > env('TICKET_NUMBERS', 99) ? 1 : $last_ticket + 1;
        return $ticket_number;
    }

    /**
     * Calculate price discount of points
     *
     * @param float $points_used_to_pay
     * @return float
     */
    public static function getPriceDiscount($points_used_to_pay)
    {
        return (($points_used_to_pay ?? 0) * 0.5);
    }

    /**
     * Calculated the total price of the OrderItems
     *
     * @param FormRequest $request
     * @return float
     */
    public static function calculateTotalPrice(StoreOrderRequest $request)
    {
        $total_price = 0;
        foreach ($request->items as $item) {
            /** @var Product $product */
            $product = Product::findOrFail($item);
            $total_price += $product->price;
        }
        return $total_price;
    }

    /**
     * Calculate gained points
     *
     * @param float $total_price
     * @return void
     */
    public static function calculateGainedPoints(float $total_price)
    {
        return floor($total_price * 0.1);
    }

    /**
     * Fake callback for payment
     *
     * @param string $type
     * @param string $reference
     * @param float $value
     * @return Response
     */
    public static function makePayment(string $type, string $reference, float $value)
    {
        $data = [
            "type" => strtolower($type),
            "reference" => $reference,
            "value" => $value
        ];

        $response = Http::post(env('PAYMENTS_ENDPOINT'), $data);

        return $response;
    }

    /**
     * Fake refund request
     *
     * @param string $type
     * @param string $reference
     * @param float $value
     * @return Response
     */
    public static function makeRefund(string $type, string $reference, float $value)
    {
        $data = [
            "type" => strtolower($type),
            "reference" => $reference,
            "value" => $value
        ];

        $response = Http::post(env('REFUNDS_ENDPOINT'), $data);

        return $response;
    }
}
