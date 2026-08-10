<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::get('/chat/{contact}', [ChatController::class, 'show'])->name('chat.show');

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');