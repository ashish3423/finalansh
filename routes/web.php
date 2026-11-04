<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EnterpriseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/', [EnterpriseController::class, 'dashboard'])->name('dashboard');

    Route::get('/salary', [SalaryController::class, 'index'])->name('salary.index');
    Route::get('/salary/export/form-b', [SalaryController::class, 'exportFormB'])->name('salary.export.form-b');
    Route::get('/salary/rules', [SalaryController::class, 'rules'])->name('salary.rules');
    Route::get('/salary/employees/{employee}/preview', [SalaryController::class, 'employeeInfo'])->name('salary.employee-preview');
    Route::middleware('can:manage-records')->group(function () {
        Route::put('/salary/rules', [SalaryController::class, 'updateRules'])->name('salary.rules.update');
        Route::post('/salary', [SalaryController::class, 'store'])->name('salary.store');
        Route::put('/salary/{salary}', [SalaryController::class, 'update'])->name('salary.update');
        Route::delete('/salary/{salary}', [SalaryController::class, 'destroy'])->name('salary.destroy');
    });

    Route::middleware('can:manage-users')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
    });

    Route::get('/{module}', [EnterpriseController::class, 'index'])->name('resources.index');
    Route::middleware('can:manage-records')->group(function () {
        Route::post('/{module}', [EnterpriseController::class, 'store'])->name('resources.store');
        Route::put('/{module}/{id}', [EnterpriseController::class, 'update'])->name('resources.update');
        Route::delete('/{module}/{id}', [EnterpriseController::class, 'destroy'])->name('resources.destroy');
    });
});
