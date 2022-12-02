<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

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
            'logged' => auth('api')->check()
        ];
    }

    /**
     * Useful to check if the token is valid or user is able to login
     *
     * @return void
     */
    public function logged()
    {
        return [
            'logged' => auth('api')->check()
        ];
    }
}
