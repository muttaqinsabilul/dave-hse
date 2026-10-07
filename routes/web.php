<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\IbprReportController;
use App\Http\Controllers\IdCardController;
use App\Http\Controllers\InspectorController;
use App\Http\Controllers\SessionAuthController;
use App\Http\Controllers\WorkerController;
use App\Support\SessionAuth;
use Illuminate\Support\Facades\Route;

Route::get('/', function (SessionAuth $auth) {
    return $auth->kind() !== null
        ? redirect()->route('dashboard')
        : redirect()->route('masuk');
})->name('home');

Route::get('/masuk', [SessionAuthController::class, 'pilihan'])->name('masuk');
Route::post('/masuk', [SessionAuthController::class, 'login'])->middleware('throttle:10,1')->name('masuk.post');
Route::post('/keluar', [SessionAuthController::class, 'logout'])->name('keluar');

Route::get('/daftar', [WorkerController::class, 'create'])->name('pekerja.daftar');
Route::post('/daftar', [WorkerController::class, 'store'])->middleware('throttle:10,1')->name('pekerja.daftar.post');

Route::middleware('auth.session')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/pekerja', [WorkerController::class, 'index'])->name('pekerja.index');
    Route::get('/pekerja/{worker}', [WorkerController::class, 'show'])->name('pekerja.show');
    Route::post('/pekerja/{worker}/toggle-lokasi', [WorkerController::class, 'toggleLokasi'])->name('pekerja.toggle-lokasi');
    Route::get('/idcard/{worker}', [IdCardController::class, 'show'])->name('idcard.show');

    Route::get('/tensi', [HealthCheckController::class, 'create'])->name('tensi.create');
    Route::post('/tensi', [HealthCheckController::class, 'store'])->name('tensi.store');

    Route::get('/ibpr', [IbprReportController::class, 'index'])->name('ibpr.index');
    Route::get('/ibpr/baru', [IbprReportController::class, 'create'])->name('ibpr.create');
    Route::post('/ibpr', [IbprReportController::class, 'store'])->name('ibpr.store');

    Route::get('/inspector', [InspectorController::class, 'index'])->name('inspector.index');
    Route::get('/inspector/baru', [InspectorController::class, 'create'])->name('inspector.create');
    Route::post('/inspector', [InspectorController::class, 'store'])->name('inspector.store');
});
