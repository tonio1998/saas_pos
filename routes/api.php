<?php

use App\Http\Controllers\API\POSApiController;
use App\Http\Controllers\API\SmsGatewayController;
use App\Http\Controllers\POS\BIRReportController;
use App\Http\Controllers\POS\SubscriptionPaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/process-sms', [SmsGatewayController::class, 'process']);
Route::post('/send-sms', [SmsGatewayController::class, 'send']);

// Direct UISFIX-compatible endpoints
Route::post('/login', [POSApiController::class, 'login']);
Route::post('/auth/google', [POSApiController::class, 'loginWithGoogle']);

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [POSApiController::class, 'login']);
    Route::post('/auth/google', [POSApiController::class, 'loginWithGoogle']);


    Route::prefix('pos')->group(function () {
        Route::get('/products/lookup-global', [POSApiController::class, 'lookupGlobalProduct']);
        Route::get('/products', [POSApiController::class, 'products']);
        Route::post('/products', [POSApiController::class, 'storeProduct']);
        Route::post('/products/{id}/stock-in', [POSApiController::class, 'stockIn']);
        Route::get('/categories', [POSApiController::class, 'categories']);
        Route::get('/units', [POSApiController::class, 'units']);



        Route::get('/customers', [POSApiController::class, 'customers']);

        Route::post('/customers', [POSApiController::class, 'storeCustomer']);
        Route::get('/customers/{id}', [POSApiController::class, 'showCustomer']);
        Route::put('/customers/{id}', [POSApiController::class, 'updateCustomer']);
        Route::post('/customers/{id}/credit-limit', [POSApiController::class, 'updateCreditLimit']);
        Route::get('/customers/{id}/ledger', [POSApiController::class, 'customerLedger']);
        Route::get('/customers/{id}/sales', [POSApiController::class, 'customerSales']);
        Route::post('/customers/collections', [POSApiController::class, 'payUtangCollection']);

        Route::get('/sales', [POSApiController::class, 'salesList']);
        Route::post('/sales', [POSApiController::class, 'createSale']);
        Route::get('/sales/{id}', [POSApiController::class, 'saleDetails']);


        Route::get('/reports/x-reading', [BIRReportController::class, 'xReading']);
        Route::get('/reports/z-reading', [BIRReportController::class, 'zReading']);
    });

    Route::post('/subscription/pay', [SubscriptionPaymentController::class, 'processPayment']);
});
