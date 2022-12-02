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
            'order_id' => $this->order_id,
            'order_local_number' => $this->order_local_number,
            'product_id' => new ProductResource($this->product),
            'status' => $this->status,
            'price' => $this->price,
            'preparation_by' => $this->preparation_by,
            'notes' => $this->notes
        ];
    }
}
