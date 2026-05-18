<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\ResetPasswordController;

use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// LOGIN PAGE
Route::get('/', function () {
    return redirect()->route('login');
});

// ANTI BRUTE FORCE LOGIN
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])
        ->middleware('throttle:5,1')
        ->name('login.post');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store']);
});

// PROTEKSI HALAMAN ADMIN
Route::middleware(['auth', 'permission:admin'])->group(function () {
    Route::get('/admin/users', [AdminController::class, 'index'])->name('admin.users');
    Route::post('/admin/users/{user}/permissions', [AdminController::class, 'updatePermissions'])->name('admin.users.permissions');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
});

// PROTEKSI HALAMAN DASHBOARD & PANEL
Route::middleware('auth')->group(function () {
    // Dashboard hanya untuk user biasa (admin langsung ke panel)
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('panel');
        }

        return view('dashboard');
    })->name('dashboard');

    Route::get('/panel', [PanelController::class, 'index'])->middleware('permission:panel')->name('panel');
    Route::get('/panel/data/realtime', [PanelController::class, 'realtimeData'])->middleware('permission:panel')->name('panel.data.realtime');
    Route::post('/panel/pump/toggle', [PanelController::class, 'togglePump'])->middleware('permission:panel')->name('panel.pump.toggle');
    
    // EXPORT ROUTES
    Route::get('/analisis/export/csv', [PanelController::class, 'exportCsv'])->middleware('permission:analisis')->name('analisis.export.csv');
    Route::get('/analisis/export/pdf', [PanelController::class, 'exportPdf'])->middleware('permission:analisis')->name('analisis.export.pdf');

    Route::get('/analisis', [PanelController::class, 'analisis'])->middleware('permission:analisis')->name('analisis');
    Route::get('/schedule', [ScheduleController::class, 'index'])->middleware('permission:schedule')->name('schedule');
    Route::post('/schedule', [ScheduleController::class, 'store'])->middleware('permission:schedule')->name('schedule.store');
    Route::get('/settings', [SettingsController::class, 'index'])->middleware('permission:settings')->name('settings.index');
    Route::post('/settings/reset', [SettingsController::class, 'resetData'])->middleware('permission:settings')->name('settings.reset');
    Route::get('/view3d', [PanelController::class, 'view3d'])->middleware('permission:view3d')->name('view3d');

    // Sensor Details
    Route::get('/sensor/dht22', [\App\Http\Controllers\SensorController::class, 'dht22'])->middleware('permission:panel')->name('sensor.dht22');
    Route::get('/sensor/ldr', [\App\Http\Controllers\SensorController::class, 'ldr'])->middleware('permission:panel')->name('sensor.ldr');
    Route::get('/api/sensor/dht22', [\App\Http\Controllers\SensorController::class, 'apiDht22'])->middleware('permission:panel')->name('api.sensor.dht22');
    Route::get('/api/sensor/ldr', [\App\Http\Controllers\SensorController::class, 'apiLdr'])->middleware('permission:panel')->name('api.sensor.ldr');


    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// PASSWORD RESET
Route::get('/forgotpw', [ResetPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgotpw', [ResetPasswordController::class, 'checkEmail'])->name('password.check');
Route::get('/resetpw', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/resetpw', [ResetPasswordController::class, 'updatePassword'])->name('password.update');
