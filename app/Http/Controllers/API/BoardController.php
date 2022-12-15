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
        $preparing = Order::preparing()
            ->orderBy('ticket_number', 'ASC')
            ->limit(env('BOARD_TICKET_LIMIT', 10))
            ->get();
        $ready = Order::ready()
            ->orderBy('ticket_number', 'ASC')
            ->limit(env('BOARD_TICKET_LIMIT', 10))
            ->get();

        return [
            'preparing' => OrderResource::collection($preparing),
            'ready' => OrderResource::collection($ready),
        ];
    }
}
