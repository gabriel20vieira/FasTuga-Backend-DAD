<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Http\Requests\StoreOrderItemRequest;
use App\Http\Requests\UpdateOrderItemRequest;
use App\Http\Resources\OrderItemResource;
use App\Models\Product;
use App\Models\Types\OrderItemStatusEnum;
use App\Models\Types\ProductType;
use Illuminate\Support\Facades\DB;

class OrderItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return OrderItemResource::collection(OrderItem::paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrderItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderItemRequest $request)
    {
        $orderItem = new OrderItem();
        $orderItem->fill($request->validated());

        $product = Product::findOrFail($this->product_id);
        if ($product->type == ProductType::HOT_DISH) {
            $orderItem->status = OrderItemStatusEnum::WAITING;
        } else {
            $orderItem->status = OrderItemStatusEnum::READY;
        }

        $orderItem = DB::transaction(function () use ($orderItem) {
            $orderItem->save();
            return $orderItem;
        });

        return new OrderItemResource($orderItem);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\Response
     */
    public function show(OrderItem $orderItem)
    {
        return new OrderItemResource($orderItem);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderItemRequest  $request
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderItemRequest $request, OrderItem $orderItem)
    {
        $orderItem->fill($request->validated());

        DB::transaction(function () use ($request, $orderItem) {
            $orderItem->save();
        });

        return new OrderItemResource($orderItem);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\Response
     */

    //TODO TESTAR DELETE e verificar as relações entre Order e OrderItem
    public function destroy(OrderItem $orderItem)
    {
        DB::transaction(function () use ($orderItem) {
            $orderItem->delete();
        });

        return new OrderItemResource($orderItem);
    }
}
