<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitizenController;
use App\Http\Controllers\CitizenNotificationController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MunicipalServiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');
Route::get('/langue/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['fr', 'en', 'ar'], true), 404);
    session(['locale' => $locale]);

    return back();
})->name('locale.switch');

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register'])->name('register.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('complaints', ComplaintController::class);
    Route::get('/reports/complaints/pdf', [ReportController::class, 'pdf'])->middleware('role:admin,employee')->name('complaints.report.pdf');
    Route::get('/reports/complaints/excel', [ReportController::class, 'excel'])->middleware('role:admin,employee')->name('complaints.report.excel');
    Route::get('/notifications', [CitizenNotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [CitizenNotificationController::class, 'markRead'])->name('notifications.read');

    Route::middleware('role:admin,employee')->group(function () {
        Route::resource('citizens', CitizenController::class);
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::resource('services', MunicipalServiceController::class)->parameters(['services' => 'municipalService']);
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    });
});
