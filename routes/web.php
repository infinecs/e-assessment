
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingsController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('web')->group(function () {
    Route::get('/admin-dashboard', function () {
        return view('assessment.index');
    });

    Route::get('/user-dashboard', function () {
        return 'Welcome User!';
    });
});

Route::get('/password.request', function () {
    return view('auth.recoverPW');
})->name('password.request');

// Settings routes – only available for logged-in users
Route::middleware(['auth'])->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.updateProfile');
    Route::post('/settings/account', [SettingsController::class, 'updateAccount'])->name('settings.updateAccount');
});
