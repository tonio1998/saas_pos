<?php

use App\Http\Controllers\AcademicContextController;
use App\Http\Controllers\ActiveSessionController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\LoginActivityController;
use App\Http\Controllers\PlatformAnalyticsController;
use App\Http\Controllers\POS\BrandController;
use App\Http\Controllers\POS\CategoryController;
use App\Http\Controllers\POS\CustomerCollectionController;
use App\Http\Controllers\POS\CustomerController;
use App\Http\Controllers\POS\CustomerCreditController;
use App\Http\Controllers\POS\ExpenseController;
use App\Http\Controllers\POS\ExpenseReportController;
use App\Http\Controllers\POS\InventoryController;
use App\Http\Controllers\POS\InventoryReportController;
use App\Http\Controllers\POS\PaymentsController;
use App\Http\Controllers\POS\POSTerminal;
use App\Http\Controllers\POS\POSTerminalController;
use App\Http\Controllers\POS\PriceHistoryController;
use App\Http\Controllers\POS\ProductController;
use App\Http\Controllers\POS\ProfitReportController;
use App\Http\Controllers\POS\PurchaseController;
use App\Http\Controllers\POS\PurchaseReportController;
use App\Http\Controllers\POS\ReturnController;
use App\Http\Controllers\POS\SalesAshShiftController;
use App\Http\Controllers\POS\SalesCashDrawerController;
use App\Http\Controllers\POS\SalesCashShiftController;
use App\Http\Controllers\POS\SalesCashTransactionController;
use App\Http\Controllers\POS\SalesController;
use App\Http\Controllers\POS\SalesReportController;
use App\Http\Controllers\POS\StockAdjustmentController;
use App\Http\Controllers\POS\StockController;
use App\Http\Controllers\POS\StockLowStockController;
use App\Http\Controllers\POS\SupplierController;
use App\Http\Controllers\POS\TenantsContextController;
use App\Http\Controllers\POS\TenantsController;
use App\Http\Controllers\POS\SubscriptionPaymentController;
use App\Http\Controllers\POS\BIRReportController;
use App\Http\Controllers\POS\UnitController;
use App\Http\Controllers\SADashboardController;
use App\Http\Controllers\TenantsDashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\GradeLevelController;
use App\Http\Controllers\SchoolLogsController;
use App\Http\Controllers\SchoolParentsController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchoolScanController;
use App\Http\Controllers\SchoolScannerController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolUsersController;
use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\SemestersController;
use App\Http\Controllers\SchoolSettingsController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\StrandsController;
use App\Http\Controllers\SchoolStudentsController;
use App\Http\Controllers\SchoolEmployeesController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\SuspiciousActivityController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    $reviews = \App\Models\POS\POSStoreReview::where('is_approved', 1)
        ->orderBy('is_featured', 'desc')
        ->latest()
        ->get();

    return view('welcome', compact('reviews'));
})->name('home');

Route::post('/submit-review', [AuthController::class, 'submitReview'])->name('reviews.store');

Route::get('/login',[AuthController::class,'showLogin'])->name('login');
Route::post('/login',[AuthController::class,'login']);
Route::get('/register',[AuthController::class,'showRegister'])->name('register');
Route::post('/register',[AuthController::class,'register']);
Route::post('/store/complete-profile', [AuthController::class, 'completeStoreProfile'])->name('store.complete-profile');
Route::post('/logout',[AuthController::class,'logout'])->name('logout');


Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::prefix('sa')->name('sa.')->middleware(['auth', 'role:SA',])->group(function () {
    Route::prefix('tenants')->name('tenants.')->group(function(){
        Route::get('/', [TenantsController::class, 'index'])->name('index');
        Route::get('/create', [TenantsController::class, 'create'])->name('create');
        Route::post('/create', [TenantsController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [TenantsController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [TenantsController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [TenantsController::class, 'destroy'])->name('destroy');
        Route::get('/show/{id}', [TenantsController::class, 'show'])->name('show');
        Route::get('/data', [TenantsController::class, 'ajaxData'])->name('data');
        Route::post('/close-context', [TenantsController::class, 'closeContext'])->name('close-context');
    });

    Route::get('/dashboard', [SADashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/data', [SADashboardController::class, 'data'])->name('.data');

    Route::prefix('backups')->name('backups.')->group(function(){
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/generate', [BackupController::class, 'generate'])->name('generate');
        Route::get('/list', [BackupController::class, 'list'])->name('list');
        Route::get('/download/{file}', [BackupController::class, 'download'])->name('download');
        Route::delete('/{file}', [BackupController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('activity-logs')->name('activity-logs.')->controller(AuditLogController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/data', 'data')->name('data');
        });

    Route::prefix('security')->name('security.')->group(function () {
            Route::prefix('login-activities')->name('login-activities.')->controller(LoginActivityController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/data', 'data')->name('data');
                });

            Route::prefix('active-sessions')->name('active-sessions.')->controller(ActiveSessionController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/data', 'data')->name('data');
                    Route::post('/revoke/{id}', 'revoke')->name('revoke');
                });

            Route::prefix('suspicious-activities')->name('suspicious-activities.')->controller(SuspiciousActivityController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/data', 'data')->name('data');
                });
        });

    Route::prefix('platform-analytics')
        ->name('platform-analytics.')
        ->group(function () {

            Route::get('/', [PlatformAnalyticsController::class, 'index'])->name('index');
            Route::get('/overview-data', [PlatformAnalyticsController::class, 'overviewData'])->name('overview-data');
            Route::get('/login-trends', [PlatformAnalyticsController::class, 'loginTrends'])->name('login-trends');
            Route::get('/security-trends', [PlatformAnalyticsController::class, 'securityTrends'])->name('security-trends');
            Route::get('/device-analytics', [PlatformAnalyticsController::class, 'deviceAnalytics'])->name('device-analytics');
            Route::get('/school-analytics', [PlatformAnalyticsController::class, 'schoolAnalytics'])->name('school-analytics');
        });


    Route::prefix('system-settings')
        ->name('system-settings.')
        ->group(function () {
            Route::get('/', [SystemSettingController::class, 'index'])->name('index');
            Route::put('/', [SystemSettingController::class, 'update'])->name('update');
        });
});

Route::prefix('support-center')->name('support-center.')->middleware(['auth', 'role:SA',])
    ->group(function () {
        Route::get('/', [SupportTicketController::class, 'index'])->name('index');
        Route::get('/datatable', [SupportTicketController::class, 'datatable'])->name('datatable');
        Route::post('/', [SupportTicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [SupportTicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('reply');
        Route::put('/{ticket}/status', [SupportTicketController::class, 'updateStatus'])->name('update-status');
    });

Route::middleware('auth')->group(function(){
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('data', [UserController::class, 'users_data'])->name('data');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/generate-password/{type}/{typeId}/{userId}',[UserController::class,'generatePassword'])
            ->name('password');
        Route::get('/{user}/roles', [UserController::class,'editRoles'])->name('roles');
        Route::get('/{user}/permissions', [UserController::class,'editPermissions'])->name('permissions');
        Route::put('/{user}/roles',[UserController::class,'updateRoles'])->name('roles.update');
        Route::put('/{user}/permissions',[UserController::class,'updatePermissions'])->name('permissions.update');
        Route::get('/{user}/change-photo', [UserController::class,'changePhoto'])->name('change-photo');
        Route::post('/upload', [UserController::class,'upload'])->name('upload');
//        Route::get('/print-id/{id}', [UserController::class, 'printID'])->name('printID');
        Route::get('/{id}/nfc', [UserController::class, 'nfc'])->name('nfc');
        Route::post('/nfc/assign', [UserController::class, 'assignNfc'])->name('nfc.assign');
    });

    Route::prefix('context')->name('context.')->group(function(){
        Route::get('/', [TenantsContextController::class, 'index'])->name('index');
        Route::post('/update', [TenantsContextController::class, 'update'])->name('update');
        Route::get('/data', [TenantsContextController::class, 'ajaxData'])->name('data');
    });


    Route::prefix('dashboard')->name('dashboard.')->group(function(){
        Route::get('/', [TenantsDashboardController::class, 'index'])->name('index');
        Route::get('/kpis', [TenantsDashboardController::class, 'kpis'])->name('kpis');
        Route::get('/analytics', [TenantsDashboardController::class, 'analyticsData'])->name('analytics');
        Route::get('/recent-sales', [TenantsDashboardController::class, 'recentSales'])->name('recent-sales');
        Route::get('/inventory-alerts', [TenantsDashboardController::class, 'inventoryAlerts'])->name('inventory-alerts');
        Route::get('/top-suki', [TenantsDashboardController::class, 'topSuki'])->name('top-suki');
    });

    Route::prefix('permissions')->name('permissions.')->group(function(){
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/create', [PermissionController::class, 'create'])->name('create');
        Route::post('/create', [PermissionController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PermissionController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PermissionController::class, 'update'])->name('update');
        Route::get('/data', [PermissionController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('roles')->name('roles.')->middleware(['auth', 'role:SA'])->group(function(){
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/create', [RoleController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [RoleController::class, 'update'])->name('update');
        Route::get('/data', [RoleController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('settings')->name('settings.')->group(function(){
        Route::get('', [SchoolSettingsController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [SchoolSettingsController::class, 'edit'])->name('edit');
        Route::post('/store', [SchoolSettingsController::class, 'store'])->name('store');
        Route::get('/data', [SchoolSettingsController::class, 'ajaxData'])->name('data');
    });
    Route::prefix('select2')->name('select2.')->group(function(){
        Route::get('roles/search',[RoleController::class,'search'])->name('roles');
        Route::get('users/search',[UserController::class,'users_search'])->name('users');
        Route::get('customers/search',[CustomerController::class,'customers_search'])->name('customers');
        Route::get('cash-drawers/search',[SalesCashDrawerController::class,'cashDrawers_search'])->name('cash-drawers');
    });


    Route::prefix('context')->name('context.')->group(function(){
        Route::get('/', [TenantsContextController::class, 'index'])->name('index');
        Route::post('/update', [TenantsContextController::class, 'update'])->name('update');
        Route::get('/data', [TenantsContextController::class, 'ajaxData'])->name('data');
    });

//    POS

    Route::prefix('terminal')->name('terminal.')->group(function () {
        Route::get('/', [POSTerminalController::class, 'index'])->name('index');
        Route::get('create', [POSTerminalController::class, 'create'])->name('create');
        Route::post('/create', [POSTerminalController::class, 'store'])->name('store');
        Route::post('/select', [POSTerminalController::class, 'select'])->name('select');

    });

    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SalesController::class, 'index'])->name('index');
        Route::get('/terminal/{sale}/sale', [SalesController::class, 'create'])->name('create');
        Route::get('/new', [SalesController::class, 'create1'])->name('create1');
        Route::post('/create', [SalesController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [SalesController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [SalesController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [SalesController::class, 'destroy'])->name('destroy');
        Route::get('/data', [SalesController::class, 'ajaxData'])->name('data');
        Route::get('/products',[SalesController::class, 'products']);
        Route::get('/{sale}/details', [SalesController::class, 'details'])->name('details');
        Route::get('/{sale}/bir-receipt', [SalesController::class, 'birReceipt'])->name('bir-receipt');
        Route::post('/complete', [SalesController::class, 'complete'])->name('complete');
        Route::get('/{sale}/sales_details', [SalesController::class, 'sales_details']);
        Route::get('/terminal/{sale}/new-sale', [SalesController::class, 'create'])->name('new');

        Route::post('/customers/quick-store', [CustomerController::class, 'quickStore'])->name('quick-store');
        Route::post('/{customers}/customer', [SalesController::class, 'updateCustomer'])->name('quick-store-2');
    });

    Route::prefix('cashiering')->name('cashiering.')->group(function () {
        Route::prefix('cash-drawers')->name('cash-drawers.')->group(function () {
            Route::get('/', [SalesCashDrawerController::class, 'index'])->name('index');
            Route::get('/create', [SalesCashDrawerController::class, 'create'])->name('create');
            Route::post('/create', [SalesCashDrawerController::class, 'store'])->name('store');
            Route::get('/view/{id}', [SalesCashDrawerController::class, 'show'])->name('show');
            Route::get('/edit/{id}', [SalesCashDrawerController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [SalesCashDrawerController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [SalesCashDrawerController::class, 'destroy'])->name('destroy');
            Route::get('/data', [SalesCashDrawerController::class, 'ajaxData'])->name('data');
        });

        Route::prefix('cash-transactions')->name('cash-transactions.')->group(function () {
            Route::get('/', [SalesCashTransactionController::class, 'index'])->name('index');
            Route::get('/create', [SalesCashTransactionController::class, 'create'])->name('create');
            Route::post('/create', [SalesCashTransactionController::class, 'store'])->name('store');
            Route::get('/view/{id}', [SalesCashTransactionController::class, 'show'])->name('show');
            Route::get('/edit/{id}', [SalesCashTransactionController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [SalesCashTransactionController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [SalesCashTransactionController::class, 'destroy'])->name('destroy');
            Route::get('/data', [SalesCashTransactionController::class, 'ajaxData'])->name('data');
        });

        Route::prefix('cash-shifts')->name('cash-shifts.')->group(function () {
            Route::get('/', [SalesCashShiftController::class, 'index'])->name('index');
//            Route::get('/{terminal}/{drawer}/create', [SalesCashShiftController::class, 'create'])->name('create');
            Route::get('/{drawer}/create', [SalesCashShiftController::class, 'create'])->name('create');
            Route::post('/create', [SalesCashShiftController::class, 'store'])->name('store');
            Route::get('/view/{id}', [SalesCashShiftController::class, 'show'])->name('show');
            Route::get('/edit/{id}', [SalesCashShiftController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [SalesCashShiftController::class, 'update'])->name('update');
            Route::get('/shifts/{id}', [SalesCashShiftController::class, 'shifts'])->name('shifts');
            Route::delete('/delete/{id}', [SalesCashShiftController::class, 'destroy'])->name('destroy');
            Route::get('/data', [SalesCashShiftController::class, 'ajaxData'])->name('data');
            Route::get('/close/{id}', [SalesCashShiftController::class, 'close'])->name('close');
            Route::post('/close/store', [SalesCashShiftController::class, 'closeStore'])->name('close.store');
        });

    });

    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/create', [ProductController::class, 'store'])->name('store');
        Route::get('/view/{id}', [ProductController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ProductController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ProductController::class, 'destroy'])->name('destroy');
        Route::get('/data', [ProductController::class, 'ajaxData'])->name('data');
        Route::get('/kpi-stats', [ProductController::class, 'kpiStats'])->name('kpi-stats');
        Route::get('/quick-view/{id}', [ProductController::class, 'quickView'])->name('quick-view');
        Route::post('/bulk-action', [ProductController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/export-csv', [ProductController::class, 'exportCsv'])->name('export-csv');
        Route::get('suggestions', [ProductController::class, 'suggestions'])->name('suggestions');
        Route::get('import-search', [ProductController::class, 'importSearch'])->name('import-search');

        Route::prefix('price-history')->name('price-history.')->group(function () {
            Route::get('/', [PriceHistoryController::class, 'index'])->name('index');
            Route::get('/create', [PriceHistoryController::class, 'create'])->name('create');
            Route::post('/create', [PriceHistoryController::class, 'store'])->name('store');
            Route::get('/view/{id}', [PriceHistoryController::class, 'show'])->name('show');
            Route::get('/edit/{id}', [PriceHistoryController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [PriceHistoryController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [PriceHistoryController::class, 'destroy'])->name('destroy');
            Route::get('/data', [PriceHistoryController::class, 'ajaxData'])->name('data');
        });

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::post('/create', [CategoryController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [CategoryController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])->name('destroy');
            Route::get('/data', [CategoryController::class, 'ajaxData'])->name('data');
        });

        Route::prefix('units')->name('units.')->group(function () {
            Route::get('/', [UnitController::class, 'index'])->name('index');
            Route::get('/create', [UnitController::class, 'create'])->name('create');
            Route::post('/create', [UnitController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [UnitController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [UnitController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [UnitController::class, 'destroy'])->name('destroy');
            Route::get('/data', [UnitController::class, 'ajaxData'])->name('data');
        });

        Route::prefix('stock')->name('stock.')->group(function () {
            Route::get('receive/{id}', [StockController::class, 'receive'])->name('receive');
            Route::post('receive/{id}', [StockController::class, 'storeReceive'])->name('receive.store');
            Route::get('history/{id}', [StockController::class, 'history'])->name('history');
            Route::get('adjustment/{id}', [StockController::class, 'adjustment'])->name('adjustment');
            Route::post('/adjustment/{id}',[StockController::class, 'storeAdjustment'])->name('adjustment.store');
        });

    });

    Route::prefix('stocks')->name('stocks.')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('index');
        Route::get('/create', [StockController::class, 'create'])->name('create');
        Route::post('/create', [StockController::class, 'store'])->name('store');
        Route::get('/view/{id}', [StockController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [StockController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [StockController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [StockController::class, 'destroy'])->name('destroy');
        Route::get('/data', [StockController::class, 'ajaxData'])->name('data');

        Route::prefix('adjustments')->name('adjustments.')->group(function () {
            Route::get('/', [StockAdjustmentController::class, 'index'])->name('index');
            Route::get('/create', [StockAdjustmentController::class, 'create'])->name('create');
            Route::post('/create', [StockAdjustmentController::class, 'store'])->name('store');
            Route::get('/view/{id}', [StockAdjustmentController::class, 'show'])->name('show');
            Route::get('/edit/{id}', [StockAdjustmentController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [StockAdjustmentController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [StockAdjustmentController::class, 'destroy'])->name('destroy');
            Route::get('/data', [StockAdjustmentController::class, 'ajaxData'])->name('data');
        });

        Route::prefix('low-stocks')->name('low-stocks.')->group(function () {
            Route::get('/', [StockLowStockController::class, 'index'])->name('index');
            Route::get('/create', [StockLowStockController::class, 'create'])->name('create');
            Route::post('/create', [StockLowStockController::class, 'store'])->name('store');
            Route::get('/view/{id}', [StockLowStockController::class, 'show'])->name('show');
            Route::get('/edit/{id}', [StockLowStockController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [StockLowStockController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [StockLowStockController::class, 'destroy'])->name('destroy');
            Route::get('/data', [StockLowStockController::class, 'ajaxData'])->name('data');
        });
    });

    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/kpis', [CustomerController::class, 'kpis'])->name('kpis');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/create', [CustomerController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [CustomerController::class, 'update'])->name('update');
        Route::get('/data', [CustomerController::class, 'ajaxData'])->name('data');

        Route::prefix('credit')->name('credit.')->group(function () {
            Route::get('/', [CustomerCreditController::class, 'index'])->name('index');
            Route::get('/create', [CustomerCreditController::class, 'create'])->name('create');
            Route::post('/create', [CustomerCreditController::class, 'store'])->name('store');
            Route::get('/view/{id}', [CustomerCreditController::class, 'show'])->name('show');
            Route::get('/edit/{id}', [CustomerCreditController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [CustomerCreditController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [CustomerCreditController::class, 'destroy'])->name('destroy');
            Route::get('/data', [CustomerCreditController::class, 'ajaxData'])->name('data');

            Route::get('/ledger/{CustomerID}', [CustomerCreditController::class, 'show'])->name('ledger.show');
            Route::get('/ledger/{CustomerID}/data', [CustomerCreditController::class, 'ledgerData'])->name('ledger.data');
        });

        Route::prefix('collections')->name('collections.')->group(function () {
            Route::get('/', [CustomerCollectionController::class, 'index'])->name('index');
            Route::get('/{customerId}/create', [CustomerCollectionController::class, 'create'])->name('create');
            Route::post('/create', [CustomerCollectionController::class, 'store'])->name('store');
            Route::get('/view/{id}', [CustomerCollectionController::class, 'show'])->name('show');
            Route::get('/edit/{id}', [CustomerCollectionController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [CustomerCollectionController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [CustomerCollectionController::class, 'destroy'])->name('destroy');
            Route::get('/data', [CustomerCollectionController::class, 'ajaxData'])->name('data');
        });
    });

    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('/create', [SupplierController::class, 'create'])->name('create');
        Route::post('/create', [SupplierController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [SupplierController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [SupplierController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [SupplierController::class, 'destroy'])->name('destroy');
        Route::get('/data', [SupplierController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::get('/', [PurchaseController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseController::class, 'create'])->name('create');
        Route::post('/create', [PurchaseController::class, 'store'])->name('store');
        Route::get('/view/{id}', [PurchaseController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [PurchaseController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PurchaseController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PurchaseController::class, 'destroy'])->name('destroy');
        Route::get('/data', [PurchaseController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/create', [ExpenseController::class, 'create'])->name('create');
        Route::post('/create', [ExpenseController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ExpenseController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ExpenseController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ExpenseController::class, 'destroy'])->name('destroy');
        Route::get('/data', [ExpenseController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('returns')->name('returns.')->group(function () {
        Route::get('/', [ReturnController::class, 'index'])->name('index');
        Route::get('/create', [ReturnController::class, 'create'])->name('create');
        Route::post('/create', [ReturnController::class, 'store'])->name('store');
        Route::get('/view/{id}', [ReturnController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [ReturnController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ReturnController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ReturnController::class, 'destroy'])->name('destroy');
        Route::get('/data', [ReturnController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentsController::class, 'index'])->name('index');
        Route::get('/create', [PaymentsController::class, 'create'])->name('create');
        Route::post('/create', [PaymentsController::class, 'store'])->name('store');
        Route::get('/view/{id}', [PaymentsController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [PaymentsController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PaymentsController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PaymentsController::class, 'destroy'])->name('destroy');
        Route::get('/data', [PaymentsController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('inventory-movements')->name('inventory-movements.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/create', [InventoryController::class, 'store'])->name('store');
        Route::get('/view/{id}', [InventoryController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [InventoryController::class, 'destroy'])->name('destroy');
        Route::get('/data', [InventoryController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [SalesReportController::class, 'index'])->name('sales');
        Route::get('/inventory', [InventoryReportController::class, 'index'])->name('inventory');
        Route::get('/expenses', [ExpenseReportController::class, 'index'])->name('expenses');
        Route::get('/profit', [ProfitReportController::class, 'index'])->name('profit');
        Route::get('/purchases', [PurchaseReportController::class, 'index'])->name('purchases');
        Route::get('/x-reading', [BIRReportController::class, 'xReading'])->name('x-reading');
        Route::get('/z-reading', [BIRReportController::class, 'zReading'])->name('z-reading');
    });

    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/checkout', [SubscriptionPaymentController::class, 'checkout'])->name('checkout');
        Route::post('/pay', [SubscriptionPaymentController::class, 'processPayment'])->name('pay');
        Route::get('/status', [SubscriptionPaymentController::class, 'checkStatus'])->name('status');
    });

});
