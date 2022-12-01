<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\API\ProductsController;
use App\Http\Controllers\API\CustomersController;
use App\Http\Controllers\API\Auth\AuthenticationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


Route::group([
    'middleware' => [
        'response.json', // 'throttle:20,10'
    ]
], function () {

    // ! AUTHENTICATION
    Route::post('login', [AuthenticationController::class, 'login'])->name('login');
    Route::post('register', [AuthenticationController::class, 'register'])->name('register');

    // ! PUBLIC
    Route::apiResource('products', ProductsController::class);
    Route::get('/image/{image}', [ImageController::class, 'show'])->name('image.show');

    Route::group(['middleware' => 'auth:api'], function () {

        // ! AUTHENTICATION
        Route::post('logout', [AuthenticationController::class, 'logout'])->name('logout');

        // ! USERS
        // The follow must be called by order of explicity, otherwise the route will be overrided
        Route::get('users/me', [UsersController::class, 'me']);
        Route::apiResource('users', UsersController::class);

        // ! CUSTOMERS
        Route::apiResource('customers', CustomersController::class);

        // ! IMAGE
        Route::post('/image', [ImageController::class, 'upload'])->name('image.upload');
    });
});
