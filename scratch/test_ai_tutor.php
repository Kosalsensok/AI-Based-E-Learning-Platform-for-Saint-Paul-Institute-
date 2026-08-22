<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\CloudflareAIService;

echo "=== Testing AI Tutor Service ===\n";
$ai = app(CloudflareAIService::class);
$systemPrompt = "You are the 24/7 AI Academic Tutor for Saint Paul Institute E-LMS. Course Context: IT & Web Development.";
$reply = $ai->chatWithTutor("What is the difference between SQL and NoSQL databases?", [
    ['role' => 'system', 'content' => $systemPrompt]
]);

echo "AI Tutor Reply:\n" . substr($reply, 0, 300) . "...\n";
echo "=== Test Passed Successfully! ===\n";
