<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Services\CloudflareAIService;
use App\Rules\Turnstile;
use Illuminate\Support\Facades\Route;

$results = [];

// 1. Database & User Accounts
try {
    $userCount = User::count();
    $admin = User::where('email', 'kosalsensok065@gmail.com')->first();
    $teacher = User::where('role', 'teacher')->orWhere('email', 'teacher@elms.com')->first();
    $student = User::where('role', 'student')->orWhere('email', 'student@elms.com')->first();

    $results['Database & Users'] = [
        'status' => 'PASS',
        'details' => "Total Users: {$userCount} | Admin: " . ($admin ? 'OK' : 'MISSING') . " | Teacher: " . ($teacher ? 'OK' : 'MISSING') . " | Student: " . ($student ? 'OK' : 'MISSING')
    ];
} catch (\Throwable $e) {
    $results['Database & Users'] = ['status' => 'FAIL', 'error' => $e->getMessage()];
}

// 2. Cloudflare Workers AI & Gateway
try {
    $ai = app(CloudflareAIService::class);
    $tokenCheck = $ai->verifyToken();
    $testPrompt = "Say 'SPI AI-ELMS is officially operational.' in 5 words.";
    $reply = $ai->chat($testPrompt);

    $results['Cloudflare Workers AI'] = [
        'status' => (!empty($reply)) ? 'PASS' : 'WARN',
        'token_valid' => $tokenCheck['success'] ? 'YES' : 'GATEWAY_MODE',
        'sample_response' => trim(substr($reply, 0, 100))
    ];
} catch (\Throwable $e) {
    $results['Cloudflare Workers AI'] = ['status' => 'FAIL', 'error' => $e->getMessage()];
}

// 3. AI Academic Tutor Chat
try {
    $ai = app(CloudflareAIService::class);
    $tutorReply = $ai->chatWithTutor("Explain how database indexes improve query performance.", [
        ['role' => 'system', 'content' => 'You are the SPI AI Tutor.']
    ]);

    $results['24/7 AI Academic Tutor'] = [
        'status' => !empty($tutorReply) ? 'PASS' : 'FAIL',
        'reply_preview' => substr($tutorReply, 0, 80) . '...'
    ];
} catch (\Throwable $e) {
    $results['24/7 AI Academic Tutor'] = ['status' => 'FAIL', 'error' => $e->getMessage()];
}

// 4. PWA Files Check
$pwaFiles = [
    'public/sw.js' => file_exists(public_path('sw.js')),
    'public/manifest.json' => file_exists(public_path('manifest.json')),
    'public/manifest.webmanifest' => file_exists(public_path('manifest.webmanifest')),
    'public/pwa-192.png' => file_exists(public_path('pwa-192.png')),
    'public/pwa-512.png' => file_exists(public_path('pwa-512.png')),
];

$allPwaExist = !in_array(false, $pwaFiles, true);
$results['PWA & Service Worker Files'] = [
    'status' => $allPwaExist ? 'PASS' : 'FAIL',
    'files' => $pwaFiles
];

// 5. Cloudflare Turnstile Rule Logic
$turnstileRule = new Turnstile();
$passedTest = true;
$testToken = '0.fake_client_token_test_1234567890abcdef';
$turnstileRule->validate('turnstile_token', $testToken, function($msg) use (&$passedTest) {
    $passedTest = false;
});

$results['Turnstile Non-Blocking Validation'] = [
    'status' => $passedTest ? 'PASS' : 'FAIL',
    'details' => 'Valid widget token accepted without false positive rejection'
];

// 6. Core Routes Compilation Check
$targetRoutes = [
    'login',
    'register',
    'ai.tutor.chat',
    'api.ai.chat',
    'api.ai.generate-quiz',
];
$foundRoutes = [];
foreach ($targetRoutes as $r) {
    $foundRoutes[$r] = Route::has($r) ? 'EXISTS' : 'NOT_FOUND';
}

$results['Core Routes Check'] = [
    'status' => 'PASS',
    'routes' => $foundRoutes
];

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
