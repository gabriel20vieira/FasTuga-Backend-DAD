<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;

class BoardController extends Controller
{
    public function index()
    {
        $preparing = Order::preparing()
            ->latest()
            ->orderBy('ticket_number', 'ASC')
            ->limit(env('BOARD_TICKET_LIMIT', 12))
            ->get();
        $ready = Order::ready()
            ->latest()
            ->orderBy('ticket_number', 'ASC')
            ->limit(env('BOARD_TICKET_LIMIT', 12))
            ->get();

        return [
            'preparing' => OrderResource::collection($preparing),
            'ready' => OrderResource::collection($ready),
        ];
    }
}
