<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

// Telegram Bot Webhooks (Supporting all standard endpoint URL conventions)
Route::match(['get', 'post'], '/telegram/webhook', [\App\Http\Controllers\Auth\TelegramAuthController::class, 'handleWebhook']);
Route::match(['get', 'post'], '/telegram-webhook', [\App\Http\Controllers\Auth\TelegramAuthController::class, 'handleWebhook']);
Route::match(['get', 'post'], '/webhook/telegram', [\App\Http\Controllers\Auth\TelegramAuthController::class, 'handleWebhook']);

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Download package សម្រាប់ offline
    Route::get('/offline/course/{course}', [Api\OfflineController::class, 'package']);
    // Sync ពី offline → server
    Route::post('/sync/progress', [Api\SyncController::class, 'progress']);
    Route::post('/sync/quiz-attempt', [Api\SyncController::class, 'quizAttempt']);
});

// Email OTP Authentication via Resend
Route::post('/auth/email-otp/send', [\App\Http\Controllers\AuthController::class, 'sendEmailOtp']);
Route::post('/auth/email-otp/verify', [\App\Http\Controllers\AuthController::class, 'verifyEmailOtp']);

