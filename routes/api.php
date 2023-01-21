<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
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

Route::get('login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('order/{order_id}', [OrderController::class, 'get']);
    Route::post('order', [OrderController::class, 'store']);
    Route::delete('order/{order}', [OrderController::class, 'destroy']);
    Route::post('order/{order}/product', [OrderController::class, 'addProduct']);
    Route::delete('order/{order}/product/{product_id}',[OrderController::class, 'deleteProduct']);
    Route::post('order/{order}/payment-method', [OrderController::class, 'paymentMethod']);

    Route::post('order/{order}/pay',[OrderController::class, 'pay']);
});



