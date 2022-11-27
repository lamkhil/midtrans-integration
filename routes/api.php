<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/google_sign_in', [UserController::class, 'google']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'store']);
Route::post('/notification/handle', [OrderController::class, 'notification']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/order/topup', [OrderController::class, 'topup']);
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/fcm', [UserController::class, 'fcm']);
    Route::post('/saldo', [UserController::class, 'saldo']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
