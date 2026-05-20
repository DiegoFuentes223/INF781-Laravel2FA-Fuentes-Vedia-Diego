<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\TwoFactorVerifyController;
use App\Http\Controllers\TwoFactorBackupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Solo auth — sin two-factor middleware
Route::middleware('auth')->group(function () {
    Route::get('/two-factor/verify', [TwoFactorVerifyController::class, 'show'])
        ->name('two-factor.verify');
    Route::post('/two-factor/verify', [TwoFactorVerifyController::class, 'verify'])
        ->name('two-factor.verify.post');

    // Ruta alternativa con código de respaldo
    Route::get('/two-factor/backup', [TwoFactorBackupController::class, 'show'])
        ->name('two-factor.backup');
    Route::post('/two-factor/backup', [TwoFactorBackupController::class, 'verify'])
        ->name('two-factor.backup.post');
});

// Auth + verificación 2FA
Route::middleware(['auth', 'verified', 'two-factor'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/two-factor/setup',    [TwoFactorController::class, 'show'])->name('two-factor.setup');
    Route::post('/two-factor/enable',  [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
});

require __DIR__.'/auth.php';