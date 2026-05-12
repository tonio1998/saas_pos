<?php

use App\Http\Controllers\AcademicContextController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\GradeLevelController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\ParentsController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\SemestersController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StrandsController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : view('auth.login');
});

Route::get('/login',[AuthController::class,'showLogin'])->name('login');
Route::post('/login',[AuthController::class,'login']);
Route::get('/register',[AuthController::class,'showRegister'])->name('register');
Route::post('/register',[AuthController::class,'register']);
Route::post('/logout',[AuthController::class,'logout'])->name('logout');


Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

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
        Route::get('/{user}/change-photo', [UserController::class,'changePhoto'])->name('change-photo');
        Route::post('/upload', [UserController::class,'upload'])->name('upload');
        Route::get('/print-id/{id}', [UserController::class, 'printID'])->name('printID');
        Route::get('/{id}/nfc', [UserController::class, 'nfc'])->name('nfc');
        Route::post('/nfc/assign', [UserController::class, 'assignNfc'])->name('nfc.assign');
    });

    Route::prefix('logs')->name('logs.')->group(function(){
        Route::get('/index', [LogsController::class, 'index'])->name('index');
        Route::get('/data', [LogsController::class, 'logs_data'])->name('data');
    });

    Route::prefix('students')->name('students.')->group(function(){
        Route::get('/index', [StudentsController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [StudentsController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [StudentsController::class, 'update'])->name('update');
        Route::get('/create', [StudentsController::class, 'create'])->name('create');
        Route::post('/create', [StudentsController::class, 'store'])->name('store');
        Route::get('/data', [StudentsController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('employees')->name('employees.')->group(function(){
        Route::get('/index', [EmployeesController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [EmployeesController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [EmployeesController::class, 'update'])->name('update');
        Route::get('/create', [EmployeesController::class, 'create'])->name('create');
        Route::post('/create', [EmployeesController::class, 'store'])->name('store');
        Route::get('/data', [EmployeesController::class, 'ajaxData'])->name('data');
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

    Route::prefix('settings')->name('settings.')->group(function(){
        Route::get('', [SettingsController::class, 'index'])->name('index');
        Route::post('', [SettingsController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [SettingsController::class, 'edit'])->name('edit');
        Route::post('/store', [SettingsController::class, 'store'])->name('store');
        Route::get('/data', [SettingsController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('school-years')->name('school_years.')->group(function(){
        Route::get('/', [SchoolYearController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [SchoolYearController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [SchoolYearController::class, 'update'])->name('update');
        Route::get('/create', [SchoolYearController::class, 'create'])->name('create');
        Route::post('/create', [SchoolYearController::class, 'store'])->name('store');
        Route::get('/data', [SchoolYearController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('semesters')->name('semesters.')->group(function(){
        Route::get('/', [SemestersController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [SemestersController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [SemestersController::class, 'update'])->name('update');
        Route::get('/create', [SemestersController::class, 'create'])->name('create');
        Route::post('/create', [SemestersController::class, 'store'])->name('store');
        Route::get('/data', [SemestersController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('grade-levels')->name('grade-levels.')->group(function(){
        Route::get('/', [GradeLevelController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [GradeLevelController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [GradeLevelController::class, 'update'])->name('update');
        Route::get('/create', [GradeLevelController::class, 'create'])->name('create');
        Route::post('/create', [GradeLevelController::class, 'store'])->name('store');
        Route::get('/data', [GradeLevelController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('strands')->name('strands.')->group(function(){
        Route::get('/', [StrandsController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [StrandsController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [StrandsController::class, 'update'])->name('update');
        Route::get('/create', [StrandsController::class, 'create'])->name('create');
        Route::post('/create', [StrandsController::class, 'store'])->name('store');
        Route::get('/data', [StrandsController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('classes')->name('classes.')->group(function(){
        Route::get('/', [ClassesController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [ClassesController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ClassesController::class, 'update'])->name('update');
        Route::get('/create', [ClassesController::class, 'create'])->name('create');
        Route::post('/create', [ClassesController::class, 'store'])->name('store');
        Route::get('/data', [ClassesController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('enrollments')->name('enrollments.')->group(function(){
        Route::get('/', [EnrollmentController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [EnrollmentController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [EnrollmentController::class, 'update'])->name('update');
        Route::get('/create', [EnrollmentController::class, 'create'])->name('create');
        Route::post('/create', [EnrollmentController::class, 'store'])->name('store');
        Route::get('/data', [EnrollmentController::class, 'ajaxData'])->name('data');
    });

    Route::get('/reports', fn() => view('pages.reports.index'));

    Route::prefix('select2')->name('select2.')->group(function(){
        Route::get('roles/search',[RoleController::class,'search'])->name('roles');
        Route::get('users/search',[UserController::class,'users_search'])->name('users');
        Route::get('guardians/search',[ParentsController::class,'parents_search'])->name('guardians');
        Route::get('employees/search',[EmployeesController::class,'employees_search'])->name('employees');
        Route::get('students/search',[StudentsController::class,'students_search'])->name('students');
        Route::get('classes/search',[ClassesController::class,'sections_search'])->name('classes');
    });

    Route::prefix('academic-context')->name('academic-context.')->group(function(){
        Route::post('/',[AcademicContextController::class,'store'])->name('store');
    });

});

Route::post('/scan',[ScanController::class,'scan'])->name('scan');
