<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\TypingController;
use App\Http\Controllers\ThemeController;

// Authentication routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Test route for development
Route::get('/test', function () {
    return view('test');
})->name('test');

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Book management
    Route::resource('books', BookController::class);
    Route::get('/books/import-from-drive', [BookController::class, 'importFromDrive'])->name('books.import-from-drive');
    Route::get('/books/{book}/typing', [TypingController::class, 'show'])->name('typing.show');
    Route::post('/typing/progress', [TypingController::class, 'saveProgress'])->name('typing.progress');
    
    // Theme management
    Route::resource('themes', ThemeController::class);
    Route::post('/themes/{theme}/activate', [ThemeController::class, 'activate'])->name('themes.activate');
});
