<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\Types\OrderStatus;
use App\Models\Types\ProductType;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Types\OrderItemStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use Illuminate\Foundation\Http\FormRequest;

class OrdersController extends Controller
{

    /**
     * Orders contructor
     */
    public function __construct()
    {
        $this->authorizeResource(Order::class, 'order');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth('api')->user()->isManager()) {
            return OrderResource::collection(Order::latest()->paginate());
        }

        return OrderResource::collection(auth('api')->user()->orders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderRequest $request)
    {
        $user = auth('api')->user() ?? null;

        $order = DB::transaction(function () use ($request, $user) {
            $last_ticket = Order::latest()->first()->ticket_number;
            $ticket_number = $last_ticket + 1 > 99 ? 1 : $last_ticket + 1;

            $order = new Order($user ? $request->only('points_used_to_pay') : []);

            $price_discount = (($order->points_used_to_pay ?? 0) * 0.5);
            $order->total_paid_with_points = $price_discount;

            $order->ticket_number = $ticket_number;
            $order->status = OrderStatus::PREPARING->value;

            if ($user) {
                $order->customer()->associate($user->customer);
            }

            $order->payment_reference = Str::random();
            $order->date = Carbon::now()->toDateString();
            $order->total_price = $this->calculateTotalPrice($request);

            if ($user) {
                $order->total_paid = ($order->total_price - $price_discount);
                $order->points_gained = floor($order->total_price * 0.1);
                $user->customer->points -= $order->points_used_to_pay;
            }

            $order->save();

            $this->assignOrderItems($request, $order);

            return $order;
        });

        return new OrderResource($order);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderRequest  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {

        DB::transaction(function () use ($request, $order) {
            $order->fill($request->validated());
            $order->save();
        });

        return new OrderResource($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        DB::transaction(function () use ($order) {
            $order->delete();
        });

        return new OrderResource($order);
    }

    /**
     * Process order items
     *
     * @param FormRequest $request
     * @param Order $order
     * @return float
     */
    private function assignOrderItems(FormRequest $request, Order $order)
    {
        $i = 0;
        foreach ($request->items as $item) {
            /** @var Product $product */
            $product = Product::findOrFail($item);
            $order->items()->create([
                'order_id' => $order->id,
                'order_local_number' => ++$i,
                'product_id' => $product->id,
                'status' => $product->type == ProductType::HOT_DISH->value ? OrderItemStatus::WAITING : OrderItemStatus::READY,
                'price' => $product->price,
            ]);
        }
    }

    /**
     * Calculated the total price of the items
     *
     * @param FormRequest $request
     * @return float
     */
    private function calculateTotalPrice(FormRequest $request)
    {
        $total_price = 0;
        foreach ($request->items as $item) {
            /** @var Product $product */
            $product = Product::findOrFail($item);
            $total_price += $product->price;
        }
        return $total_price;
    }
}
