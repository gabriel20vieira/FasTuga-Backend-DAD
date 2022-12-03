<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\API\ProductsController;
use App\Http\Controllers\API\CustomersController;
use App\Http\Controllers\API\Auth\AuthenticationController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\OrdersController;
use App\Http\Controllers\API\PaymentsController;

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


// ─── Hybrid Access ───────────────────────────────────────────────────────────

Route::apiResource('products', ProductsController::class);

Route::apiResource('customers', CustomersController::class);

Route::apiResource('orders', OrdersController::class);

// ─── With Full Authentication ────────────────────────────────────────────────

Route::group(['middleware' => 'auth:api'], function () {

    Route::post('logout', [AuthenticationController::class, 'logout'])->name('logout');

    Route::get('users/me', [UsersController::class, 'me']);
    Route::apiResource('users', UsersController::class);

    Route::post('/image', [ImageController::class, 'upload'])->name('image.upload');
});
