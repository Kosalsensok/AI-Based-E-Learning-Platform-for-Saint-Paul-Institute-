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
    Route::post('verify-reset-otp', [PasswordResetLinkController::class, 'verifyOtp'])->name('password.verify_otp');
    Route::post('reset-password', [PasswordResetLinkController::class, 'resetPassword'])->name('password.update');
    Route::post('api/auth/forgot-password', [PasswordResetLinkController::class, 'store'])->name('api.password.email');
    Route::post('api/auth/verify-reset-otp', [PasswordResetLinkController::class, 'verifyOtp'])->name('api.password.verify_otp');
    Route::post('api/auth/reset-password', [PasswordResetLinkController::class, 'resetPassword'])->name('api.password.update');

    // Email OTP Authentication via Resend
    Route::post('auth/email-otp/send', [\App\Http\Controllers\AuthController::class, 'sendEmailOtp'])->name('auth.email-otp.send');
    Route::post('auth/email-otp/verify', [\App\Http\Controllers\AuthController::class, 'verifyEmailOtp'])->name('auth.email-otp.verify');
});

// ─── Telegram OAuth Widget & Direct Redirect Routes ───
Route::match(['get', 'post'], 'auth/telegram', [\App\Http\Controllers\Auth\TelegramAuthController::class, 'handleCallback'])->name('auth.telegram');
Route::get('auth/telegram/callback', [\App\Http\Controllers\Auth\TelegramAuthController::class, 'handleCallback'])->name('auth.telegram.callback');
Route::match(['get', 'post'], 'api/auth/telegram', [\App\Http\Controllers\Auth\TelegramAuthController::class, 'handleCallback'])->name('api.auth.telegram');
Route::get('api/auth/telegram/callback', [\App\Http\Controllers\Auth\TelegramAuthController::class, 'handleCallback'])->name('api.auth.telegram.callback');

// ─── Clerk & Google OAuth Routes ───
Route::get('auth/google/redirect', [\App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::match(['get', 'post'], 'auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::match(['get', 'post'], 'auth/clerk', [\App\Http\Controllers\Auth\ClerkAuthController::class, 'handleCallback'])->name('auth.clerk');
Route::match(['get', 'post'], 'auth/google', [\App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])->name('auth.google');
Route::get('auth/clerk/callback', [\App\Http\Controllers\Auth\ClerkAuthController::class, 'handleCallback'])->name('auth.clerk.callback');
Route::match(['get', 'post'], 'api/auth/clerk', [\App\Http\Controllers\Auth\ClerkAuthController::class, 'handleCallback'])->name('api.auth.clerk');

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
