<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderItemRequest;
use App\Http\Resources\OrderItemResource;
use App\Models\OrderItem;
use App\Models\Types\OrderStatus;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{

    /**
     * Contructor
     *
     */
    public function __construct()
    {
        $this->authorizeResource(OrderItem::class, 'orderitem');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderItemRequest $request, OrderItem $orderItem)
    {
        abort_if(
            $orderItem->order->status != OrderStatus::PREPARING->value,
            400,
            "Order Item cannot be changed. Ordes has another state."
        );

        $updated = DB::transaction(function () use ($request, $orderItem) {
            $orderItem->update($request->safe()->only('status'));
            return $orderItem->save();
        });

        return (new OrderItemResource($orderItem))->additional([
            'message' => $updated ? "Item updated successfully." : "Item was not updated."
        ]);
    }
}
