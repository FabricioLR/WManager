<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::middleware('guest')->group(function (){
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::middleware(['auth'])->group(function (){
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::get('/chat/{contact}', [ChatController::class, 'show'])->name('chat.show');
    
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/user', [DashboardController::class, 'store'])->name('dashboard.user.store');
    });
});