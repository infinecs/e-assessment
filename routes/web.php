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
Route::delete('/events/{id}', [EventsController::class, 'destroy'])->name('events.destroy');
Route::put('/events/{id}', [EventsController::class, 'update'])->name('events.update');
Route::get('/events/{id}/details', [EventsController::class, 'getEventDetails'])->name('events.details');
Route::get('/category/{id}/topics', [EventsController::class, 'getCategoryTopics'])->name('category.topics');
Route::get('/events/{id}/details', [EventsController::class, 'getEventDetails'])->name('events.details');
Route::get('/category/{id}/topics', [EventsController::class, 'getCategoryTopics'])->name('category.topics');

Route::view('/results', 'assessment.results')->name('results');
Route::get('/results', [AssessmentResultController::class, 'index'])->name('results');

Route::delete('/assessment-results/delete', [AssessmentResultController::class, 'bulkDelete'])
    ->name('assessment.bulkDelete');

// Route for assessment details modal AJAX
Route::get('/assessment/{id}/details', [AssessmentResultController::class, 'details']);

    


Route::view('/category', 'assessment.category')->name('category');
Route::get('/category', [AssessmentCategoryController::class, 'index'])->name('category');
Route::get('/category/{id}/details', [AssessmentCategoryController::class, 'getCategoryDetails']);
Route::delete('/category/{id}', [AssessmentCategoryController::class, 'destroy'])->name('category.destroy');
Route::put('/category/{id}', [AssessmentCategoryController::class, 'update'])->name('category.update');

Route::view('/topic', 'assessment.topic')->name('topic');
Route::get('/topic', [AssessmentTopicController::class, 'index'])->name('topic');
Route::delete('/topic/{id}', [AssessmentTopicController::class, 'destroy'])->name('topic.destroy');
Route::put('/topic/{id}', [AssessmentTopicController::class, 'update'])->name('topic.update');
Route::get('/topic/{id}/details', [AssessmentTopicController::class, 'getTopicDetails'])->name('topic.details');

Route::view('/question', 'assessment.question')->name('question');
Route::get('/question', [AssessmentQuestionController::class, 'index'])->name('question');

// Question CRUD routes
Route::delete('/question/{id}', [AssessmentQuestionController::class, 'destroy'])->name('question.destroy');
Route::put('/question/{id}', [AssessmentQuestionController::class, 'update'])->name('question.update');
Route::get('/question/{id}/details', [AssessmentQuestionController::class, 'getQuestionDetails'])->name('question.details');

// Question answers routes
Route::get('/question/{id}/answers', [AssessmentQuestionController::class, 'getAnswers']);
Route::post('/question/{id}/answers', [AssessmentQuestionController::class, 'updateAnswers']);



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

Route::post('/quiz/{eventCode}/clear-answers', [QuizController::class, 'clearAnswers'])
    ->name('quiz.clearAnswers');

// Session management routes for cross-browser prevention
Route::post('/quiz/{eventCode}/check-active-session', [QuizController::class, 'checkActiveSession'])
    ->name('quiz.checkActiveSession');

Route::post('/quiz/{eventCode}/clear-active-session', [QuizController::class, 'clearActiveSession'])
    ->name('quiz.clearActiveSession');

Route::post('/quiz/{eventCode}/heartbeat', [QuizController::class, 'heartbeat'])
    ->name('quiz.heartbeat');

    Route::get('/quiz/{eventCode}/results', [QuizController::class, 'showResults'])
    ->name('quiz.results');