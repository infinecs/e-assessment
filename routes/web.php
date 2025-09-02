
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
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use App\Models\Participant;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// CSRF token refresh route
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Removed unprotected admin/user routes - these are now handled by protected routes below

Route::get('/password.request', function () {
    return view('auth.recoverPW');
})->name('password.request');

// Settings routes – only available for logged-in users
Route::middleware(['auth'])->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.updateProfile');
    Route::post('/settings/account', [SettingsController::class, 'updateAccount'])->name('settings.updateAccount');
    
    // User dashboard route - for general authenticated users
    Route::get('/user', function () {
        return view('user');
    })->name('user.dashboard');
});



Route::middleware(['rolelog:admin'])->group(function () {
Route::get('/admin', [AdminController::class, 'index'])->name('assessment.index');
//Route for events
Route::view('/events', 'assessment.events')->name('events');
Route::get('/events', [EventsController::class, 'index'])->name('events');
Route::post('/events', [EventsController::class, 'store'])->name('events.store');
Route::post('/events/bulk-delete', [EventsController::class, 'bulkDestroy'])->name('events.bulkDestroy');
Route::delete('/events/{id}', [EventsController::class, 'destroy'])->name('events.destroy');
Route::put('/events/{id}', [EventsController::class, 'update'])->name('events.update');
Route::get('/events/{id}/details', [EventsController::class, 'getEventDetails'])->name('events.details');
Route::post('/events/{id}/weightages', [EventsController::class, 'updateWeightages'])->name('events.updateWeightages');
Route::get('/events/export-excel', [EventsController::class, 'exportExcel'])->name('events.exportExcel');

Route::view('/results', 'assessment.results')->name('results');
Route::get('/results', [AssessmentResultController::class, 'index'])->name('results');
Route::delete('/assessment-results/delete', [AssessmentResultController::class, 'bulkDelete'])
    ->name('assessment.bulkDelete');
// Route for assessment details modal AJAX
Route::get('/assessment/{id}/details', [AssessmentResultController::class, 'details']);
// Route for Excel export
Route::get('/assessment/export-excel', [AssessmentResultController::class, 'exportExcel'])
    ->name('assessment.exportExcel');

    

//Route for category
Route::get('/category/{id}/topics', [EventsController::class, 'getCategoryTopics'])->name('category.topics');
Route::view('/category', 'assessment.category')->name('category');
Route::get('/category', [AssessmentCategoryController::class, 'index'])->name('category');
Route::post('/category', [AssessmentCategoryController::class, 'store'])->name('category.store');
Route::post('/category/bulk-delete', [AssessmentCategoryController::class, 'bulkDestroy'])->name('category.bulk-delete');
Route::get('/category/{id}/details', [AssessmentCategoryController::class, 'getCategoryDetails']);
Route::delete('/category/{id}', [AssessmentCategoryController::class, 'destroy'])->name('category.destroy');
Route::put('/category/{id}', [AssessmentCategoryController::class, 'update'])->name('category.update');

Route::view('/topic', 'assessment.topic')->name('topic');
Route::get('/topic', [AssessmentTopicController::class, 'index'])->name('topic');
Route::post('/topic', [AssessmentTopicController::class, 'store'])->name('topic.store');
Route::delete('/topic/bulk-delete', [AssessmentTopicController::class, 'bulkDestroy'])->name('topic.bulkDestroy');
Route::delete('/topic/{id}', [AssessmentTopicController::class, 'destroy'])->name('topic.destroy');
Route::put('/topic/{id}', [AssessmentTopicController::class, 'update'])->name('topic.update');
Route::get('/topic/{id}/details', [AssessmentTopicController::class, 'getTopicDetails'])->name('topic.details');
Route::get('/topic/export-excel', [AssessmentTopicController::class, 'exportExcel'])->name('topic.exportExcel');

Route::view('/question', 'assessment.question')->name('question');
Route::get('/question', [AssessmentQuestionController::class, 'index'])->name('question');

// Question CRUD routes
Route::post('/question', [AssessmentQuestionController::class, 'store'])->name('question.store');
Route::delete('/question/bulk-delete', [AssessmentQuestionController::class, 'bulkDestroy'])->name('question.bulkDestroy');
Route::delete('/question/{id}', [AssessmentQuestionController::class, 'destroy'])->name('question.destroy');
Route::put('/question/{id}', [AssessmentQuestionController::class, 'update'])->name('question.update');
Route::get('/question/{id}/details', [AssessmentQuestionController::class, 'getQuestionDetails'])->name('question.details');
Route::get('/question/export-excel', [AssessmentQuestionController::class, 'exportExcel'])->name('question.exportExcel');


Route::post('/participants', [ParticipantsController::class, 'store'])->name('participants.store');
//Route for events
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/search', [UserController::class, 'search'])->name('users.search'); // Move this up
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
Route::post('/users/bulk-delete', [UserController::class, 'bulkDestroy'])->name('users.bulk-delete');

// Question answers routes - admin only
Route::get('/question/{id}/answers', [AssessmentQuestionController::class, 'getAnswers']);
Route::post('/question/{id}/answers', [AssessmentQuestionController::class, 'updateAnswers']);
});

// Participant registration routes - intentionally public for participants to register
Route::get('/participantRegister/{eventCode}', [ParticipantsController::class, 'showRegisterForm'])->name('participantRegister.show');
Route::post('/participantRegister/{eventCode}', [ParticipantsController::class, 'register'])->name('participantRegister.store');

// Quiz session management routes - require participant authentication
Route::middleware(['participant.auth'])->group(function() {
    Route::group(['prefix' => 'quiz/{eventCode}'], function() {
        Route::post('/check-active-session', [QuizController::class, 'checkActiveSession'])->name('quiz.checkActiveSession');
        Route::post('/takeover-session', [QuizController::class, 'takeoverSession'])->name('quiz.takeoverSession');
        Route::post('/heartbeat', [QuizController::class, 'heartbeat'])->name('quiz.heartbeat');
        Route::post('/clear-active-session', [QuizController::class, 'clearActiveSession'])->name('quiz.clearActiveSession');
        Route::post('/save-answer', [QuizController::class, 'saveAnswer'])->name('quiz.saveAnswer');
        Route::post('/clear-answers', [QuizController::class, 'clearAnswers'])->name('quiz.clearAnswers');
        
        Route::get('/', [QuizController::class, 'showQuiz'])->name('quiz.show');
        Route::post('/submit', [QuizController::class, 'submitQuiz'])->name('quiz.submit');
        Route::get('/results', [QuizController::class, 'showResults'])->name('quiz.results');
        Route::post('/auto-submit', [QuizController::class, 'autoSubmitQuiz'])->name('quiz.autoSubmit');
    });
    
    // Force submit blank answers if user leaves quiz page
    Route::post('/quiz/{eventCode}/force-submit-blank', [QuizController::class, 'forceSubmitBlank'])->name('quiz.forceSubmitBlank');
});


