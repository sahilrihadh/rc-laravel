<?php

use Illuminate\Support\Facades\Route;


require __DIR__.'/auth.php';

// Home
Route::get('/', function () {
    return view('welcome');
})->name('home');



// Auth Routes (to be created)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');


Route::get('/thank-you', function () {
    return view('thank-you');
})->name('thank.you');

Route::post('/logout', function () {
    auth()->logout();
    return redirect('/');
})->name('logout');


// Test route
Route::get('/test-alpine', function () {
    return view('test');
});

Route::get('/', function () {
    return view('welcome');
})->name('dashboard');