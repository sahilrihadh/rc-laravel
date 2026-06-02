<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test-message', function () {
    return response()->json(['message' => 'Axios is working perfectly! 🎉']);
});

// Protected routes (will add authentication later)
Route::middleware('auth:sanctum')->group(function () {
    // Poll routes
    Route::post('/poll/vote', [App\Http\Controllers\PollController::class, 'vote']);
    Route::get('/poll/results/{pollId}', [App\Http\Controllers\PollController::class, 'getResults']);
    
    // Question routes
    Route::post('/question/ask', [App\Http\Controllers\QuestionController::class, 'store']);
    Route::get('/questions/{webinarSessionId}', [App\Http\Controllers\QuestionController::class, 'index']);
    
    // Webinar tracking
    Route::post('/webinar/track-start', [App\Http\Controllers\WebinarController::class, 'trackStart']);
    Route::post('/webinar/track-complete', [App\Http\Controllers\WebinarController::class, 'trackComplete']);
});