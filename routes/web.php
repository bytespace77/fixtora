<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SlaController;
use App\Http\Controllers\ProfileController; // ✅ Step 17
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SchedulingController;
use App\Http\Controllers\IntegrationRequestController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // Dashboard (+ export ?export=pdf|excel, + search ?q=..., + range ?range=24h|7d|30d|90d)
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/home/export', [HomeController::class, 'index'])->name('home.export');

    // Tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/comments', [TicketController::class, 'addComment'])->name('tickets.comments.store');
    Route::delete('/tickets/{ticket}/comments/{comment}', [TicketController::class, 'deleteComment'])->name('tickets.comments.destroy');
    Route::patch('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
    // Attachments
    Route::post('/tickets/{ticket}/attachments', [TicketController::class, 'uploadAttachment'])->name('tickets.attachments.store');
    Route::delete('/tickets/{ticket}/attachments/{attachment}', [TicketController::class, 'deleteAttachment'])->name('tickets.attachments.destroy');

    // Tasks — include POST fallback for AJAX _method spoofing
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update.post');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // SLA Monitor
    Route::get('/sla-monitor', [SlaController::class, 'index'])->name('sla-monitor.index');

    // Settings
    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings.index');

    // Help
    Route::get('/help', function () {
        return view('help.index');
    })->name('help.index');

    // ✅ Step 17: Profile — wired to real controller
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Notifications
    Route::get('/notifications', function () {
        return view('notificatons.index');
    })->name('notifications.index');

    // Scheduling (calendar + upcoming tasks by due date)
    Route::get('/scheduling', [SchedulingController::class, 'index'])->name('scheduling.index');

    // Integrations
    Route::get('/integrations/custom-request', [IntegrationRequestController::class, 'create'])
        ->name('integrations.custom-request.create');
    Route::post('/integrations/custom-request', [IntegrationRequestController::class, 'store'])
        ->name('integrations.custom-request.store');
});