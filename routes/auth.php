<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;


Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/fetch-cities', [RegisteredUserController::class, 'fetchCities'])->name('fetch.cities');
});



Route::post('/login-process', function (LoginRequest $request) {
    try {
        $request->authenticate();
        
        // Make sure session is saved
        $request->session()->regenerate();
        
        // Verify user is actually logged in
        if (!Auth::check()) {
            throw new \Exception('Authentication failed');
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'redirect_url' => route('webcast'),
            'user' => Auth::user()->email_id // For debugging
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 401);
    }
})->name('login.process');

Route::get('/fetch-cities', [RegisteredUserController::class, 'fetchCities'])->name('fetch.cities'); 




// Protected routes (require authentication)
Route::middleware('auth')->group(function () {
    // Page Routes
    Route::get('/webcast', [PageController::class, 'webcast'])->name('webcast');
    Route::get('/previous-sessions', [PageController::class, 'previousSessions'])->name('previous-sessions');
    Route::get('/player', [PageController::class, 'player'])->name('player');
    
    // AJAX Routes for Webcast
    Route::post('/submit-question', [PageController::class, 'submitQuestion'])->name('submit-question');
    Route::post('/get-questions', [PageController::class, 'getQuestions'])->name('get-questions');
    Route::post('/check-poll', [PageController::class, 'checkPoll'])->name('check-poll');
    Route::post('/submit-vote', [PageController::class, 'submitVote'])->name('submit-vote');
    Route::post('/store-reaction', [PageController::class, 'storeReaction'])->name('store-reaction');
    Route::post('/track-activity', [PageController::class, 'trackActivity'])->name('track-activity');
});

// Fallback route
Route::get('/', function () {
    return redirect()->route('login');
});