<?php

use App\Http\Controllers\Auth\EmployeeLoginController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\EmployeeWorkController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\RelievingLetterController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\RecycleBinController;
use App\Http\Controllers\ActivityLogsController;

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

// this will create /broadcasting/auth route
Broadcast::routes(['middleware' => ['auth']]);

// Admin routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('employees', EmployeeController::class);
    Route::post('/employees/{employee}/inline-update', [EmployeeController::class, 'inlineUpdate'])->name('employees.inline-update');
    Route::resource('reviews', ReviewController::class);
    Route::resource('leaves', LeaveController::class);
    Route::put('/leaves/{leave}/status', [LeaveController::class, 'updateStatus'])->name('leaves.status');
    Route::post('/leaves/{id}/inline-update', [LeaveController::class, 'inlineUpdate'])->name('leaves.inline-update');
    Route::get('/dashboard/online', [DashboardController::class, 'getOnlineEmployees']);

    Route::post('/employees/{id}/upload-documents', [EmployeeController::class, 'uploadDocuments'])->name('employees.upload-documents');
    Route::get('/employees/{id}/documents', [EmployeeController::class, 'getDocuments'])->name('employees.documents');
    Route::delete('/employees/{employeeId}/documents/{documentId}', [EmployeeController::class, 'deleteDocument'])->name('employees.documents.delete');

    Route::get('/employees/{id}/relieving-letter', [RelievingLetterController::class, 'show'])->name('employees.relieving-letter');
    Route::post('/employees/{id}/relieving-letter/save', [RelievingLetterController::class, 'save'])->name('employees.relieving-letter.save');

    Route::post('/reviews/{id}/inline-update', [ReviewController::class, 'inlineUpdate'])->name('reviews.inline-update');

    Route::resource('assessments', AssessmentController::class);

    Route::get('/settings', [SettingController::class, 'index'])->name('setting');
    Route::resource('vendors', VendorController::class);
    Route::post('/vendors/{vendor}/inline-update', [VendorController::class, 'inlineUpdate']);
    
    Route::get('/notifications/list', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/admin/notifications/send', [NotificationController::class, 'sendNotification'])->name('admin.notifications.send');
    Route::post('/notifications/delete-by-data', [NotificationController::class, 'deleteByData'])->name('notifications.deleteByData');
    
    Route::resource('candidates', CandidateController::class);
    Route::post('candidates/inline-update', [CandidateController::class, 'inlineUpdate'])->name('candidates.inline.update');
    
    Route::get('/recycle-bin', [RecycleBinController::class, 'index']);
    Route::post('/recycle-bin/{id}/restore', [RecycleBinController::class, 'restore'])
        ->name('recycle.restore');
    Route::delete('/recycle-bin/{id}', [RecycleBinController::class, 'destroy'])
        ->name('recycle.delete');
    
    Route::get('/activity-logs', [ActivityLogsController::class, 'index']);
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
    
    Route::resource('/employee/reviews-list', ReviewController::class);
    Route::post('/employee/reviews-list/{id}/inline-update', [ReviewController::class, 'inlineUpdate'])->name('reviews.inline-update');
    Route::post('/employee/reviews-list/store', [ReviewController::class, 'store'])->name('employee.review.store');
    Route::delete('/employee/reviews-list/delete/{id}', [ReviewController::class, 'destroy'])->name('employee.review.destroy');

    // Employee Leave Routes
    Route::get('/employee/leaves', [LeaveController::class, 'employeeIndex'])->name('employee.leaves.index');
    Route::get('/employee/leaves/create', [LeaveController::class, 'employeeCreate'])->name('employee.leaves.create');
    Route::post('/employee/leaves', [LeaveController::class, 'employeeStore'])->name('employee.leaves.store');
    Route::get('/employee/leaves/{leave}', [LeaveController::class, 'employeeShow'])->name('employee.leaves.show');
	
    Route::post('/employee/work/timer', [EmployeeWorkController::class, 'timerWork'])->name('employee.work.timer');
    Route::get('/employee/work', [EmployeeWorkController::class, 'workIndex'])->name('employee.work.index');
    Route::post('/employee/{id}/inline-update', [EmployeeWorkController::class, 'inlineUpdate'])->name('employee.work.inline-update');

    Route::get('/employee/profile', [EmployeeController::class, 'profile'])->name('employee.profile');

    Route::resource('/employee/reminders', ReminderController::class);
    Route::post('/employee/reminders/{reminder}/inline-update', [ReminderController::class, 'inlineUpdate'])->name('employee.reminders.inlineUpdate');
    
    // Mark a reminder completed
    Route::post('/employee/reminders/{reminder}/complete', [ReminderController::class, 'complete'])->name('employee.reminders.complete');
    
    Route::get('/notifications', [NotificationController::class, 'index'])->name('employee.notifications');
    Route::post('/notifications/mark-read', function (Request $request) {
        auth('employee')->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.markRead');
    
    Route::get('/employee/team', [TeamController::class, 'teamIndex'])->name('employee.team.index');
    Route::post('/employee/team/store', [TeamController::class, 'store'])->name('employee.team.store');
    Route::delete('/employee/team/destroy/{id}', [TeamController::class, 'destroy'])->name('employee.team.destroy');
    Route::post('/employee/team/leave/{id}/approve', [TeamController::class, 'approve'])->name('employee.team.leave.approve');
    Route::post('/employee/team/leave/{id}/reject', [TeamController::class, 'reject'])->name('employee.team.leave.reject');
    
    Route::get('/employee/{id}', [EmployeeController::class, 'details'])->name('employee.details');
    
    Route::post('/employee/leaves/{id}/reply', [LeaveController::class, 'addReply'])->name('employee.leave.reply');
    
    Route::get('/employee/work/export-pdf', [EmployeeWorkController::class, 'exportPdf'])->name('employee.work.export.pdf');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/leaves/{id}/replies', [LeaveController::class, 'getReplies']);
Route::post('/leaves/{id}/replies', [LeaveController::class, 'addReply']);

Route::get('/leaves/export/pdf', [LeaveController::class, 'exportPdf'])->name('leaves.export.pdf');

Route::get('/employees/{id}/attendance/filter', [EmployeeController::class, 'attendanceFilter'])->name('employees.attendance.filter');
Route::get('/employees/{id}/attendance/export-pdf', [EmployeeController::class, 'attendanceExportPdf'])->name('employees.attendance.export.pdf');