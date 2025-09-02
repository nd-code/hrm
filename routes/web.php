<?php

use App\Http\Controllers\Auth\EmployeeLoginController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Admin routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('employees', EmployeeController::class);
    Route::post('/employees/{employee}/inline-update', [EmployeeController::class, 'inlineUpdate'])->name('employees.inline-update');
	Route::resource('reviews', ReviewController::class);
	Route::resource('leaves', LeaveController::class);
	Route::put('/leaves/{leave}/status', [LeaveController::class, 'updateStatus'])->name('leaves.status');
});

// Admin Auth (Breeze)
Route::get('/', fn() => redirect('/login'));
require __DIR__.'/auth.php';

// Employee Auth
Route::get('/employee/login', [EmployeeLoginController::class, 'showLoginForm'])->name('employee.login');
Route::post('/employee/login', [EmployeeLoginController::class, 'login']);
Route::post('/employee/logout', [EmployeeLoginController::class, 'logout'])->name('employee.logout');

// Employee Dashboard
Route::middleware(['auth:employee'])->group(function () {
    Route::get('/employee/dashboard', fn() => view('employee.dashboard'))->name('employee.dashboard');

    // Employee Leave Routes
    Route::get('/employee/leaves', [LeaveController::class, 'employeeIndex'])->name('employee.leaves.index');
    Route::get('/employee/leaves/create', [LeaveController::class, 'employeeCreate'])->name('employee.leaves.create');
    Route::post('/employee/leaves', [LeaveController::class, 'employeeStore'])->name('employee.leaves.store');
    Route::get('/employee/leaves/{leave}', [LeaveController::class, 'employeeShow'])->name('employee.leaves.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});