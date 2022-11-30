<?php

use App\Http\Controllers\API\Auth\AuthenticationController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\ProductsController;
use App\Http\Controllers\API\UsersController;
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

// ─── Public Routes ───────────────────────────────────────────────────

Route::post('login', [AuthenticationController::class, 'login'])->name('login');
Route::post('register', [AuthenticationController::class, 'register'])->name('register');

// ─── Authentication Protection ───────────────────────────────────────

Route::group([
    'middleware' => [
        'auth:api',
        'response.json'
        // 'throttle:20,10',
        // 'verified'
    ]
], function () {
    Route::post('logout', [AuthenticationController::class, 'logout'])->name('logout');

    Route::get('/image/{image}', [ImageController::class, 'show'])->name('image.show');
    Route::post('/image', [ImageController::class, 'upload'])->name('image.upload');

    Route::apiResource('products', ProductsController::class);

    // The follow must be called by order of explicity, otherwise the route will be overrided
    Route::get('users/me', [UsersController::class, 'me']);
    Route::apiResource('users', UsersController::class);
});
