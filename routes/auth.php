<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::post('reset-password', [PasswordResetLinkController::class, 'resetPassword'])->name('password.update');
    Route::post('api/auth/forgot-password', [PasswordResetLinkController::class, 'store'])->name('api.password.email');
    Route::post('api/auth/reset-password', [PasswordResetLinkController::class, 'resetPassword'])->name('api.password.update');
});

// ─── Telegram OAuth Widget & Direct Redirect Routes ───
Route::match(['get', 'post'], 'auth/telegram', [\App\Http\Controllers\Auth\TelegramAuthController::class, 'handleCallback'])->name('auth.telegram');
Route::get('auth/telegram/callback', [\App\Http\Controllers\Auth\TelegramAuthController::class, 'handleCallback'])->name('auth.telegram.callback');
Route::match(['get', 'post'], 'api/auth/telegram', [\App\Http\Controllers\Auth\TelegramAuthController::class, 'handleCallback'])->name('api.auth.telegram');
Route::get('api/auth/telegram/callback', [\App\Http\Controllers\Auth\TelegramAuthController::class, 'handleCallback'])->name('api.auth.telegram.callback');

// ─── Clerk & Google OAuth Routes ───
Route::match(['get', 'post'], 'auth/clerk', [\App\Http\Controllers\Auth\ClerkAuthController::class, 'handleCallback'])->name('auth.clerk');
Route::match(['get', 'post'], 'auth/google', [\App\Http\Controllers\Auth\ClerkAuthController::class, 'handleCallback'])->name('auth.google');
Route::get('auth/clerk/callback', [\App\Http\Controllers\Auth\ClerkAuthController::class, 'handleCallback'])->name('auth.clerk.callback');
Route::match(['get', 'post'], 'api/auth/clerk', [\App\Http\Controllers\Auth\ClerkAuthController::class, 'handleCallback'])->name('api.auth.clerk');

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
