<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TicketController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // Tickets - use controller
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::patch('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
    
    // Tasks
    Route::get('/tasks', function () {
        return view('tasks.index');
    })->name('tasks.index');
    
    // Reports
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');
    
    // SLA Monitor
    Route::get('/sla-monitor', function () {
        return view('sla-monitor.index');
    })->name('sla-monitor.index');
    
    // Settings
    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings.index');
    
    // Help
    Route::get('/help', function () {
        return view('help.index');
    })->name('help.index');
    
    // Profile
    Route::get('/profile', function () {
        return view('profile.show');
    })->name('profile.show');
    
    // Notifications
    Route::get('/notifications', function () {
        return view('notifications.index');
    })->name('notifications.index');
});