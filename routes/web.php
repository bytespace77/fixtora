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
use App\Http\Controllers\RoleController;

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
    Route::post('/sla-monitor/configure', [SlaController::class, 'configure'])->name('sla-monitor.configure');



    // Help
    Route::get('/help', function () {
        return view('help.index');
    })->name('help.index');

    // ✅ Step 17: Profile — wired to real controller
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/session/destroy', [ProfileController::class, 'destroySession'])->name('profile.session.destroy');

    // Notifications
    Route::get('/notifications', function () {
        return view('notificatons.index');
    })->name('notifications.index');

    // Scheduling (calendar + upcoming tasks by due date)
    Route::get('/scheduling', [SchedulingController::class, 'index'])->name('scheduling.index');

    // Roles & Permissions
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::patch('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::post('/roles/{role}/permissions', [RoleController::class, 'savePermissions'])->name('roles.permissions');
    Route::post('/roles/{role}/association', [RoleController::class, 'saveAssociation'])->name('roles.association');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    // Integrations
    Route::get('/integrations', [App\Http\Controllers\IntegrationController::class, 'index'])->name('integrations.index');
    Route::get('/integrations/requests', [IntegrationRequestController::class, 'index'])->name('integrations.requests.index');
    Route::patch('/integrations/requests/{integrationRequest}', [IntegrationRequestController::class, 'update'])->name('integrations.requests.update');
    Route::post('/integrations/{integration}/connect', [App\Http\Controllers\IntegrationController::class, 'connect'])->name('integrations.connect');
    Route::get('/integrations/{integration}/configure', [App\Http\Controllers\IntegrationController::class, 'configure'])->name('integrations.configure');
    Route::post('/integrations/{integration}/configure', [App\Http\Controllers\IntegrationController::class, 'saveConfig'])->name('integrations.configure.store');
    Route::delete('/integrations/{integration}/disconnect', [App\Http\Controllers\IntegrationController::class, 'disconnect'])->name('integrations.disconnect');
    Route::get('/integrations/custom-request', [IntegrationRequestController::class, 'create'])
        ->name('integrations.custom-request.create');
    Route::post('/integrations/custom-request', [IntegrationRequestController::class, 'store'])
        ->name('integrations.custom-request.store');
});