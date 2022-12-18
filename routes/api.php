<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\API\ProductsController;
use App\Http\Controllers\API\CustomersController;
use App\Http\Controllers\API\Auth\AuthenticationController;
use App\Http\Controllers\API\BoardController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\OrderItemController;
use App\Http\Controllers\API\OrdersController;
use App\Http\Controllers\API\PaymentsController;
use App\Http\Controllers\API\StatisticsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ─── Public Routes ───────────────────────────────────────────────────────────


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/logged', [HomeController::class, 'logged'])->name('logged');

Route::post('login', [AuthenticationController::class, 'login'])->name('login');
Route::post('register', [AuthenticationController::class, 'register'])->name('register');

Route::get('/image/{image}', [ImageController::class, 'show'])->name('image.show');

Route::get('/board', [BoardController::class, 'index'])->name('board');


// ─── Hybrid Access ───────────────────────────────────────────────────────────

Route::apiResource('products', ProductsController::class);

Route::apiResource('customers', CustomersController::class);

Route::apiResource('orders', OrdersController::class);

// ─── With Full Authentication ────────────────────────────────────────────────

Route::group(['middleware' => 'auth:api'], function () {

    Route::post('logout', [AuthenticationController::class, 'logout'])->name('logout');

    Route::post('change-password', [UsersController::class, 'changePassword']);
    Route::get('users/me', [UsersController::class, 'me']);
    Route::apiResource('users', UsersController::class);

    Route::apiResource('orderitems', OrderItemController::class)->only('index', 'update');

    Route::get('statistics', [StatisticsController::class, 'statistics']);
});
