<?php

use App\Http\Controllers\API\SmsGatewayController;
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


use App\Http\Controllers\API\POSApiController;
use App\Http\Controllers\POS\BIRReportController;
use App\Http\Controllers\POS\SubscriptionPaymentController;

Route::post('/process-sms', [SmsGatewayController::class, 'process']);
Route::post('/send-sms', [SmsGatewayController::class, 'send']);

// React Native Android POS API v1 Routes
Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [POSApiController::class, 'login']);

    Route::prefix('pos')->group(function () {
        Route::get('/products', [POSApiController::class, 'products']);
        Route::get('/customers', [POSApiController::class, 'customers']);
        Route::post('/customers', [POSApiController::class, 'storeCustomer']);
        Route::post('/sales', [POSApiController::class, 'createSale']);
        Route::get('/sales/{id}', [POSApiController::class, 'saleDetails']);
        Route::get('/reports/x-reading', [BIRReportController::class, 'xReading']);
        Route::get('/reports/z-reading', [BIRReportController::class, 'zReading']);
    });

    Route::post('/subscription/pay', [SubscriptionPaymentController::class, 'processPayment']);
});

