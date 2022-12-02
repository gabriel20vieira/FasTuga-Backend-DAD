<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function index()
    {
        return [
            'payment' => auth('api')->user()->orders()->where('payment_reference', '!=', 'null')->where('total_paid', '=', 'null')->get()
        ];
    }

    public function pay(string $reference)
    {
        return [
            'payment' => auth('api')->user()->orders()->where('payment_reference', '!=', 'null')->where('total_paid', '=', 'null')->get()
        ];
    }
}
