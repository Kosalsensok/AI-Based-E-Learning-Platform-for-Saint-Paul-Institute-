<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Download package សម្រាប់ offline
    Route::get('/offline/course/{course}', [Api\OfflineController::class, 'package']);
    // Sync ពី offline → server
    Route::post('/sync/progress', [Api\SyncController::class, 'progress']);
    Route::post('/sync/quiz-attempt', [Api\SyncController::class, 'quizAttempt']);
});
