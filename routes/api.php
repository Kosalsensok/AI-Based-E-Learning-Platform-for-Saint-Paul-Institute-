<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

// Telegram Bot Webhooks (Supporting all standard endpoint URL conventions)
Route::match(['get', 'post'], '/telegram/webhook', [\App\Http\Controllers\Auth\TelegramAuthController::class, 'handleWebhook']);
Route::match(['get', 'post'], '/telegram-webhook', [\App\Http\Controllers\Auth\TelegramAuthController::class, 'handleWebhook']);
Route::match(['get', 'post'], '/webhook/telegram', [\App\Http\Controllers\Auth\TelegramAuthController::class, 'handleWebhook']);

Route::middleware(['web', 'auth'])->prefix('v1')->group(function () {
    // Download package សម្រាប់ offline
    Route::get('/offline/course/{course}', [Api\OfflineController::class, 'package']);
    // Sync ពី offline → server
    Route::post('/sync/progress', [Api\SyncController::class, 'progress']);
    Route::post('/sync/quiz-attempt', [Api\SyncController::class, 'quizAttempt']);
});

Route::middleware('auth:sanctum')->prefix('v1/token')->group(function () {
    Route::post('/sync/progress', [Api\SyncController::class, 'progress']);
    Route::post('/sync/quiz-attempt', [Api\SyncController::class, 'quizAttempt']);
});

// Email OTP Authentication via Resend
Route::post('/auth/email-otp/send', [\App\Http\Controllers\AuthController::class, 'sendEmailOtp']);
Route::post('/auth/email-otp/verify', [\App\Http\Controllers\AuthController::class, 'verifyEmailOtp']);

// ─── Cloudflare Workers AI & AI Gateway Endpoints ───
Route::prefix('ai')->group(function () {
    Route::get('/verify', [Api\CloudflareAIController::class, 'verify']);
    Route::post('/verify', [Api\CloudflareAIController::class, 'verify']);
    Route::post('/chat', [Api\CloudflareAIController::class, 'chat']);
    Route::post('/recommendation', [Api\CloudflareAIController::class, 'recommendation']);
    Route::post('/generate-quiz', [Api\CloudflareAIController::class, 'generateQuiz']);
    Route::post('/summarize', [Api\CloudflareAIController::class, 'summarize']);
    Route::post('/code-review', [Api\CloudflareAIController::class, 'codeReview']);
    Route::post('/agri-diagnosis', [Api\CloudflareAIController::class, 'agriDiagnosis']);
    Route::post('/english-review', [Api\CloudflareAIController::class, 'englishReview']);
    Route::post('/social-work-case', [Api\CloudflareAIController::class, 'socialWorkCase']);
});

