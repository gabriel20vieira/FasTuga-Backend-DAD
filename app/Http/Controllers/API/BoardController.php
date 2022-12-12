<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;

class BoardController extends Controller
{
    public function index()
    {
        $preparing = Order::latest()->limit(env('TICKET_NUMBERS', 99))->preparing()->get();
        $ready = Order::latest()->limit(env('TICKET_NUMBERS', 99))->ready()->get();

        return [
            'preparing' => OrderResource::collection($preparing),
            'ready' => OrderResource::collection($ready),
        ];
    }
}
