<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\ParentsController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\ResidentsController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login',[AuthController::class,'showLogin'])->name('login');
Route::post('/login',[AuthController::class,'login']);
Route::get('/register',[AuthController::class,'showRegister'])->name('register');
Route::post('/register',[AuthController::class,'register']);

Route::post('/logout',[AuthController::class,'logout'])->name('logout');
Route::middleware('auth')->group(function(){
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
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
    });

    Route::prefix('logs')->name('logs.')->group(function(){
        Route::get('/index', [LogsController::class, 'index'])->name('index');
        Route::get('/data', [LogsController::class, 'logs_data'])->name('data');
    });

    Route::prefix('residents')->name('residents.')->group(function(){
        Route::get('/index', [ResidentsController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [ResidentsController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ResidentsController::class, 'update'])->name('update');
        Route::get('/create', [ResidentsController::class, 'create'])->name('create');
        Route::post('/create', [ResidentsController::class, 'store'])->name('store');
        Route::get('/data', [ResidentsController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('teachers')->name('teachers.')->group(function(){
        Route::get('/index', [TeachersController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [TeachersController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [TeachersController::class, 'update'])->name('update');
        Route::get('/create', [TeachersController::class, 'create'])->name('create');
        Route::post('/create', [TeachersController::class, 'store'])->name('store');
        Route::get('/data', [TeachersController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('parents')->name('parents.')->group(function(){
        Route::get('/index', [ParentsController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [ParentsController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ParentsController::class, 'update'])->name('update');
        Route::get('/create', [ParentsController::class, 'create'])->name('create');
        Route::post('/create', [ParentsController::class, 'store'])->name('store');
        Route::get('/data', [ParentsController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('scanner')->name('scanner.')->group(function(){
        Route::get('/index', [ScannerController::class, 'index'])->name('index');
    });

    Route::prefix('permissions')->name('permissions.')->group(function(){
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/create', [PermissionController::class, 'create'])->name('create');
        Route::post('/create', [PermissionController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PermissionController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PermissionController::class, 'update'])->name('update');
        Route::get('/data', [PermissionController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('roles')->name('roles.')->group(function(){
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/create', [RoleController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [RoleController::class, 'update'])->name('update');
        Route::get('/data', [RoleController::class, 'ajaxData'])->name('data');
    });

    Route::get('/reports', fn() => view('pages.reports.index'));
    Route::get('/settings', fn() => view('pages.settings.index'));

    Route::prefix('select2')->name('select2.')->group(function(){
        Route::get('roles/search',[RoleController::class,'search'])->name('roles');
        Route::get('users/search',[UserController::class,'users_search'])->name('users');
        Route::get('guardians/search',[ParentsController::class,'parents_search'])->name('guardians');
    });

});
