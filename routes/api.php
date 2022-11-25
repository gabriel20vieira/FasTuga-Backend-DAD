<?php

use App\Http\Controllers\API\OrderItemsController;
use App\Http\Controllers\API\ProductsController;
use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\API\OrdersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// TODO Substitute later for passport
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v1'], function () {
    Route::apiResource('products', ProductsController::class);
});

//* USERS *//
Route::get('users', [UsersController::class, 'index']);
Route::get('users/{user}', [UsersController::class, 'show']);
Route::post('users', [UsersController::class, 'store']);
Route::put('users/{user}', [UsersController::class, 'update']);
Route::delete('users/{user}', [UsersController::class, 'destroy']);

//* ORDERS *//
Route::apiResource('orders', OrdersController::class);

//* ORDER ITEMS *//
Route::apiResource('orderitems', OrderItemsController::class);
