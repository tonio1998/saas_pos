<?php

use App\Http\Controllers\AcademicContextController;
use App\Http\Controllers\ActiveSessionController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\LoginActivityController;
use App\Http\Controllers\PlatformAnalyticsController;
use App\Http\Controllers\SADashboardController;
use App\Http\Controllers\SchoolDashboardController;
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
    return Auth::check()
        ? redirect()->route('dashboard.index')
        : view('auth.login');
});

Route::get('/login',[AuthController::class,'showLogin'])->name('login');
Route::post('/login',[AuthController::class,'login']);
Route::get('/register',[AuthController::class,'showRegister'])->name('register');
Route::post('/register',[AuthController::class,'register']);
Route::post('/logout',[AuthController::class,'logout'])->name('logout');


Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::prefix('sa')->name('sa.')->middleware([
    'auth',
    'role:SA',
])->group(function () {
    Route::prefix('schools')
        ->name('schools.')->group(function(){
            Route::get('/', [SchoolController::class, 'index'])->name('index');
            Route::get('/create', [SchoolController::class, 'create'])->name('create');
            Route::post('/', [SchoolController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [SchoolController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [SchoolController::class, 'update'])->name('update');
            Route::get('/data', [SchoolController::class, 'ajaxData'])->name('data');
            Route::get('/show/{id}', [SchoolController::class, 'show'])->name('show');

            Route::post(
                '/close-context',
                [SchoolController::class, 'closeContext']
            )->name('close-context');
        });

    Route::get('/dashboard', [
        SADashboardController::class,
        'index'
    ])->name('dashboard.index');
    Route::get('/dashboard/data', [
        SADashboardController::class,
        'data'
    ])->name('.data');

    Route::prefix('backups')->name('backups.')->group(function(){
        Route::get('/', [BackupController::class, 'index'])
            ->name('index');

        Route::post('/generate', [BackupController::class, 'generate'])
            ->name('generate');

        Route::get('/list', [BackupController::class, 'list'])
            ->name('list');

        Route::get('/download/{file}', [BackupController::class, 'download'])
            ->name('download');

        Route::delete('/{file}', [BackupController::class, 'destroy'])
            ->name('destroy');
    });

    Route::prefix('activity-logs')
        ->name('activity-logs.')
        ->controller(AuditLogController::class)
        ->group(function () {

            Route::get('/', 'index')
                ->name('index');

            Route::get('/data', 'data')->name('data');

        });

    Route::prefix('security')
        ->name('security.')
        ->group(function () {

            Route::prefix('login-activities')
                ->name('login-activities.')
                ->controller(LoginActivityController::class)
                ->group(function () {

                    Route::get(
                        '/',
                        'index'
                    )->name('index');

                    Route::get(
                        '/data',
                        'data'
                    )->name('data');

                });

            Route::prefix('active-sessions')
                ->name('active-sessions.')
                ->controller(ActiveSessionController::class)
                ->group(function () {

                    Route::get(
                        '/',
                        'index'
                    )->name('index');

                    Route::get(
                        '/data',
                        'data'
                    )->name('data');

                    Route::post(
                        '/revoke/{id}',
                        'revoke'
                    )->name('revoke');

                });

            Route::prefix('suspicious-activities')
                ->name('suspicious-activities.')
                ->controller(
                    SuspiciousActivityController::class
                )
                ->group(function () {

                    Route::get(
                        '/',
                        'index'
                    )->name('index');

                    Route::get(
                        '/data',
                        'data'
                    )->name('data');

                });

        });

    Route::prefix('platform-analytics')
        ->name('platform-analytics.')
        ->group(function () {

            Route::get('/', [PlatformAnalyticsController::class, 'index'])
                ->name('index');

            Route::get('/overview-data', [PlatformAnalyticsController::class, 'overviewData'])
                ->name('overview-data');

            Route::get('/login-trends', [PlatformAnalyticsController::class, 'loginTrends'])
                ->name('login-trends');

            Route::get('/security-trends', [PlatformAnalyticsController::class, 'securityTrends'])
                ->name('security-trends');

            Route::get('/device-analytics', [PlatformAnalyticsController::class, 'deviceAnalytics'])
                ->name('device-analytics');

            Route::get('/school-analytics', [PlatformAnalyticsController::class, 'schoolAnalytics'])
            ->name('school-analytics');

        });

    Route::prefix('system-settings')
        ->name('system-settings.')
        ->group(function () {

            Route::get(
                '/',
                [SystemSettingController::class, 'index']
            )->name('index');

            Route::put(
                '/',
                [SystemSettingController::class, 'update']
            )->name('update');
        });

});

Route::prefix('support-center')
    ->name('support-center.')
    ->middleware([
        'auth',
        'role:SA',
    ])
    ->group(function () {

        Route::get(
            '/',
            [SupportTicketController::class, 'index']
        )->name('index');

        Route::get(
            '/datatable',
            [SupportTicketController::class, 'datatable']
        )->name('datatable');

        Route::post(
            '/',
            [SupportTicketController::class, 'store']
        )->name('store');

        Route::get(
            '/{ticket}',
            [SupportTicketController::class, 'show']
        )->name('show');

        Route::post(
            '/{ticket}/reply',
            [SupportTicketController::class, 'reply']
        )->name('reply');

        Route::put(
            '/{ticket}/status',
            [SupportTicketController::class, 'updateStatus']
        )->name('update-status');
    });

Route::middleware('auth')->group(function(){
    Route::prefix('dashboard')->name('dashboard.')->group(function(){
        Route::get('/', [SchoolDashboardController::class, 'index'])->name('index');
        Route::get('/data', [SchoolDashboardController::class, 'data'])->name('data');
    });

    Route::prefix('school-users')->name('school-users.')->group(function(){
        Route::get('/', [SchoolUsersController::class, 'index'])->name('index');
        Route::get('data', [SchoolUsersController::class, 'users_data'])->name('data');
        Route::get('/create', [SchoolUsersController::class, 'create'])->name('create');
        Route::post('/', [SchoolUsersController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [SchoolUsersController::class, 'edit'])->name('edit');
        Route::put('/{user}', [SchoolUsersController::class, 'update'])->name('update');
        Route::delete('/{user}', [SchoolUsersController::class, 'destroy'])->name('destroy');
        Route::get('/{user}/roles', [SchoolUsersController::class,'editRoles'])->name('roles');
        Route::get('/{user}/permissions', [SchoolUsersController::class,'editPermissions'])->name('permissions');
        Route::put('/{user}/roles',[SchoolUsersController::class,'updateRoles'])->name('roles.update');
        Route::put('/{user}/permissions',[SchoolUsersController::class,'updatePermissions'])->name('permissions.update');
        Route::get('/{user}/change-photo', [SchoolUsersController::class,'changePhoto'])->name('change-photo');
        Route::post('/upload', [SchoolUsersController::class,'upload'])->name('upload');
        Route::get('/print-id/{id}', [SchoolUsersController::class, 'printID'])->name('printID');
        Route::get('/{id}/nfc', [SchoolUsersController::class, 'nfc'])->name('nfc');
        Route::post('/nfc/assign', [SchoolUsersController::class, 'assignNfc'])->name('nfc.assign');
    });

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
        Route::get('/index', [SchoolLogsController::class, 'index'])->name('index');
        Route::get('/data', [SchoolLogsController::class, 'logs_data'])->name('data');
        Route::get('/users', [SchoolLogsController::class, 'users'])->name('users');
        Route::get('/users/data', [SchoolLogsController::class, 'users_data'])->name('users.data');
    });

    Route::prefix('students')->name('students.')->group(function(){
        Route::get('/index', [SchoolStudentsController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [SchoolStudentsController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [SchoolStudentsController::class, 'update'])->name('update');
        Route::get('/create', [SchoolStudentsController::class, 'create'])->name('create');
        Route::post('/create', [SchoolStudentsController::class, 'store'])->name('store');
        Route::get('/data', [SchoolStudentsController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('employees')->name('employees.')->group(function(){
        Route::get('/index', [SchoolEmployeesController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [SchoolEmployeesController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [SchoolEmployeesController::class, 'update'])->name('update');
        Route::get('/create', [SchoolEmployeesController::class, 'create'])->name('create');
        Route::post('/create', [SchoolEmployeesController::class, 'store'])->name('store');
        Route::get('/data', [SchoolEmployeesController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('parents')->name('parents.')->group(function(){
        Route::get('/index', [SchoolParentsController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [SchoolParentsController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [SchoolParentsController::class, 'update'])->name('update');
        Route::get('/create', [SchoolParentsController::class, 'create'])->name('create');
        Route::post('/create', [SchoolParentsController::class, 'store'])->name('store');
        Route::get('/data', [SchoolParentsController::class, 'ajaxData'])->name('data');
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
        Route::get('', [SchoolSettingsController::class, 'index'])->name('index');
        Route::post('', [SchoolSettingsController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [SchoolSettingsController::class, 'edit'])->name('edit');
        Route::post('/store', [SchoolSettingsController::class, 'store'])->name('store');
        Route::get('/data', [SchoolSettingsController::class, 'ajaxData'])->name('data');
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

    Route::prefix('reports')->name('reports.')->group(function(){
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/gate-logs/data', [ReportsController::class, 'gateLogsData'])->name('gate-logs.data');
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

    Route::prefix('sms')->name('sms.')->group(function(){
       Route::get('/',[SmsController::class,'index'])->name('index');
       Route::get('data',[SmsController::class,'ajaxData'])->name('data');
    });

    Route::prefix('select2')->name('select2.')->group(function(){
        Route::get('roles/search',[RoleController::class,'search'])->name('roles');
        Route::get('users/search',[UserController::class,'users_search'])->name('users');
        Route::get('guardians/search',[SchoolParentsController::class,'parents_search'])->name('guardians');
        Route::get('employees/search',[SchoolEmployeesController::class,'employees_search'])->name('employees');
        Route::get('students/search',[SchoolStudentsController::class,'students_search'])->name('students');
        Route::get('classes/search',[ClassesController::class,'sections_search'])->name('classes');
    });


    Route::prefix('academic-context')->name('academic-context.')->group(function(){
        Route::post('/',[AcademicContextController::class,'store'])->name('store');
    });

});

Route::post('/scan',[SchoolScanController::class,'scan'])->name('scan');
Route::prefix('scanner')->name('scanner.')->group(function(){
    Route::get('/', [SchoolScannerController::class, 'index'])->name('index');
    Route::post('/send-sms', [SchoolScannerController::class, 'send']);
});
