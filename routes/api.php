<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\POSApiController;
use App\Http\Controllers\API\SmsGatewayController;
use App\Http\Controllers\POS\BIRReportController;
use App\Http\Controllers\POS\SubscriptionPaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::post('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::post('/auth/google', [AuthController::class, 'loginWithGoogle']);
    Route::get('/auth/me', [AuthController::class, 'me']);


    Route::prefix('pos')->group(function () {
        Route::get('/products/lookup-global', [POSApiController::class, 'lookupGlobalProduct']);
        Route::get('/products', [POSApiController::class, 'products']);
        Route::post('/products', [POSApiController::class, 'storeProduct']);
        Route::put('/products/{id}', [POSApiController::class, 'updateProduct']);
        Route::post('/products/{id}', [POSApiController::class, 'updateProduct']);
        Route::post('/products/{id}/stock-in', [POSApiController::class, 'stockIn']);
        Route::get('/products/{id}/performance', [POSApiController::class, 'productPerformance']);
        Route::get('/products/{id}', [POSApiController::class, 'showProduct']);
        Route::get('/categories', [POSApiController::class, 'categories']);
        Route::get('/units', [POSApiController::class, 'units']);



        Route::get('/customers', [POSApiController::class, 'customers']);
        Route::post('/customers', [POSApiController::class, 'storeCustomer']);
        Route::post('/customers/collections', [POSApiController::class, 'payUtangCollection']);
        Route::get('/customers/{id}', [POSApiController::class, 'showCustomer']);
        Route::put('/customers/{id}', [POSApiController::class, 'updateCustomer']);
        Route::post('/customers/{id}/credit-limit', [POSApiController::class, 'updateCreditLimit']);
        Route::get('/customers/{id}/ledger', [POSApiController::class, 'customerLedger']);
        Route::get('/dashboard', [POSApiController::class, 'dashboard']);
        Route::get('/promotions', [POSApiController::class, 'promotions']);
        Route::post('/promotions', [POSApiController::class, 'storePromotion']);
        Route::put('/promotions/{id}', [POSApiController::class, 'updatePromotion']);
        Route::post('/promotions/{id}/toggle', [POSApiController::class, 'togglePromotion']);
        Route::delete('/promotions/{id}', [POSApiController::class, 'destroyPromotion']);
        Route::get('/promotions/{id}/items', [POSApiController::class, 'promoItems']);
        Route::post('/promotions/{id}/items', [POSApiController::class, 'addPromoItems']);
        Route::delete('/promotions/{id}/items/{itemId}', [POSApiController::class, 'removePromoItem']);
        Route::post('/promotions/{id}/items/clear', [POSApiController::class, 'clearPromoItems']);
        Route::get('/cash-shifts/active', [POSApiController::class, 'activeShift']);
        Route::post('/cash-shifts/open', [POSApiController::class, 'openShift']);
        Route::post('/cash-shifts/close', [POSApiController::class, 'closeShift']);
        Route::get('/orders/switcher', [POSApiController::class, 'ordersSwitcher']);

        Route::get('/sales', [POSApiController::class, 'salesList']);
        Route::post('/sales', [POSApiController::class, 'createSale']);
        Route::get('/reports/x-reading', [BIRReportController::class, 'xReading']);
        Route::get('/reports/z-reading', [BIRReportController::class, 'zReading']);

        Route::get('/settings', [POSApiController::class, 'getSettings']);
        Route::post('/settings', [POSApiController::class, 'updateSettings']);
    });

    Route::post('/subscription/pay', [SubscriptionPaymentController::class, 'processPayment']);
});
