<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SlaController;
use App\Http\Controllers\ProfileController; // ✅ Step 17

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::patch('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');

    // Tasks — include POST fallback for AJAX _method spoofing
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update.post');

    // Reports
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');

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
});