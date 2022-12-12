<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'identification' => $this->identification,
            'order_id' => $this->order_id,
            'order_local_number' => $this->order_local_number,
            'status' => $this->status,
            'price' => $this->price,
            'product' => new ProductResource($this->product),
            'preparated' => new UserResource($this->preparated)
        ];
    }
}
