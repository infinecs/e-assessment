
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

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