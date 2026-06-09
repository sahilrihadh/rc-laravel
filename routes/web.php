<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\PollController;
use App\Http\Controllers\Admin\VisitorController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\AdminUserController;

// ==================== PUBLIC ROUTES ====================

// Home/Welcome Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Webinar Join Redirect
Route::get('/webinar/join', function () {
    return redirect('https://royalcanin.sociolive.in/');
})->name('webinar.join');

// Thank You Page (after registration)
Route::get('/thank-you', function () {
    return view('thank-you');
})->name('thank.you');

// ==================== ADMIN ROUTES ====================
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {

    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::resource('polls', PollController::class);
        Route::post('polls/{id}/toggle-status', [PollController::class, 'toggleStatus'])->name('polls.toggle-status');

        Route::get('users', [UserController::class, 'index'])->name('users');
        Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('questions', [QuestionController::class, 'index'])->name('questions');
        Route::delete('questions/{id}', [QuestionController::class, 'destroy'])->name('questions.destroy');
    });
});

// ==================== AUTH ROUTES ====================
require __DIR__ . '/auth.php';

// ==================== PROTECTED ROUTES ====================
Route::middleware('auth')->group(function () {
    // Dashboard/Webcast Pages
    Route::get('/webcast', [PageController::class, 'webcast'])->name('webcast');
    Route::get('/previous-sessions', [PageController::class, 'previousSessions'])->name('previous-sessions');
    Route::post('/send-certificate', [PageController::class, 'sendCertificate'])->name('send-certificate');
    Route::get('/player', [PageController::class, 'player'])->name('player');

    // AJAX Routes for Webcast
    Route::post('/submit-question', [PageController::class, 'submitQuestion'])->name('submit-question');
    Route::post('/get-questions', [PageController::class, 'getQuestions'])->name('get-questions');
    Route::post('/check-poll', [PageController::class, 'checkPoll'])->name('check-poll');
    Route::post('/submit-vote', [PageController::class, 'submitVote'])->name('submit-vote');
    Route::post('/store-reaction', [PageController::class, 'storeReaction'])->name('store-reaction');
    Route::post('/track-activity', [PageController::class, 'trackActivity'])->name('track-activity');

    // Logout
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

// ==================== FALLBACK ROUTE (MUST BE LAST) ====================
Route::fallback(function () {
    return redirect()->route('home');
});
