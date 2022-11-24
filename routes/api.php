<?php

use App\Http\Controllers\API\Auth\AuthenticationController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\ProductsController;
use App\Http\Controllers\API\UsersController;
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

Route::group([
    'as' => 'v1.',
    'prefix' => 'v1',
], function () {

    // ─── Public Routes ───────────────────────────────────────────────────

    Route::post('login', [AuthenticationController::class, 'login'])->name('api.login');
    Route::post('register', [AuthenticationController::class, 'register'])->name('api.register');


    // ─── Authentication Protection ───────────────────────────────────────

    Route::group([
        'middleware' => [
            'auth:api'
        ]
    ], function () {
        Route::post('/upload/image', [ImageController::class, 'upload'])->name('upload.image');

        Route::apiResource('products', ProductsController::class);
        Route::apiResource('users', UsersController::class);
    });
});
