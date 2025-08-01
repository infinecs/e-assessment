<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ParticipantsController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\AssessmentResultController;
use App\Http\Controllers\AssessmentCategoryController;
use App\Http\Controllers\AssessmentTopicController;
use App\Http\Controllers\AssessmentQuestionController;
use App\Http\Controllers\QuizController;

use Illuminate\Http\Request;
use App\Models\Participant;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('web')->group(function () {
    Route::get('/admin', function () {
        return view('assessment.index');
    });

    Route::get('/user', function () {
        return view('user');
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



Route::middleware(['rolelog:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('assessment.index');
});
Route::view('/events', 'assessment.events')->name('events');
Route::get('/events', [EventsController::class, 'index'])->name('events');

Route::view('/results', 'assessment.results')->name('results');
Route::get('/results', [AssessmentResultController::class, 'index'])->name('results');

Route::delete('/assessment-results/delete', [AssessmentResultController::class, 'bulkDelete'])
    ->name('assessment.bulkDelete');

// Route for assessment details modal AJAX
Route::get('/assessment/{id}/details', [AssessmentResultController::class, 'details']);

    


Route::view('/category', 'assessment.category')->name('category');
Route::get('/category', [AssessmentCategoryController::class, 'index'])->name('category');

Route::view('/topic', 'assessment.topic')->name('topic');
Route::get('/topic', [AssessmentTopicController::class, 'index'])->name('topic');

Route::view('/question', 'assessment.question')->name('question');
Route::get('/question', [AssessmentQuestionController::class, 'index'])->name('question');



Route::get('/participantRegister/{eventCode}', [ParticipantsController::class, 'showRegisterForm'])
    ->name('participantRegister.show');

Route::post('/participantRegister/{eventCode}', [ParticipantsController::class, 'register'])
    ->name('participantRegister.store');

Route::post('/participants', [ParticipantsController::class, 'store'])
    ->name('participants.store');



Route::get('/quiz/{eventCode}', [QuizController::class, 'showQuiz'])->name('quiz.show');


Route::post('/quiz/{eventCode}/submit', [QuizController::class, 'submitQuiz'])->name('quiz.submit');

Route::post('/quiz/{eventCode}/save-answer', [QuizController::class, 'saveAnswer'])
    ->name('quiz.saveAnswer');

    Route::get('/quiz/{eventCode}/results', [QuizController::class, 'showResults'])
    ->name('quiz.results');