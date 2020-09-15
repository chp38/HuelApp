<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ShopifyController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/customers/average-order-value', [CustomerController::class, 'customersAverage']);
    Route::get('/customer/{id}/average-order-value', [CustomerController::class, 'customerAverage']);
    Route::get('/variant/{id}/average-order-value', [CustomerController::class, 'variantAverage']);
});

Route::name('shopify.')->group(function () {
    Route::post('/shopify/create/customer', [ShopifyController::class, 'createCustomer'])->name('create.customer');
    Route::post('/shopify/create/product', [ShopifyController::class, 'createProduct'])->name('create.product');
    Route::post('/shopify/create/order', [ShopifyController::class, 'createOrder'])->name('create.order');
});