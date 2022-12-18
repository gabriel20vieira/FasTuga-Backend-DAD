<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Starting endpoint of API
     *
     * @return void
     */
    public function index()
    {
        return [
            'message' => 'Welcome to the FasTuga API. Please be nice to others and cause rampage. Sincerely, The Team.',
        ];
    }
}
