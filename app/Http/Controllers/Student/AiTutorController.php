<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AiTutorController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'english');
        return Inertia::render('Student/AiTutor/Index', [
            'activeTab' => $tab,
        ]);
    }

    public function english(Request $request)
    {
        return Inertia::render('Student/AiTutor/EnglishTutor');
    }

    public function chat(Request $request)
    {
        return Inertia::render('Student/AiTutor/ChatSupport');
    }

    public function feedback(Request $request)
    {
        return Inertia::render('Student/AiTutor/InstantFeedback');
    }

    public function askTutor(Request $request, \App\Services\CloudflareAIService $aiService)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'context_course' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:100',
        ]);

        $message = trim($request->input('message'));
        $course = $request->input('context_course', 'General Academic Study');
        $major = $request->input('major', 'General');

        $systemPrompt = "You are the 24/7 AI Academic Tutor for Saint Paul Institute E-LMS. Course Context: {$course} (Major: {$major}). Provide concise, structured, friendly, and practical explanations in Khmer and English when helpful.";

        try {
            $reply = $aiService->chatWithTutor($message, [
                ['role' => 'system', 'content' => $systemPrompt]
            ]);

            return response()->json([
                'success' => true,
                'reply' => $reply,
                'model' => config('services.cloudflare.ai_default_model', '@cf/meta/llama-3.1-8b-instruct'),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('AI Tutor chat fallback triggered: ' . $e->getMessage());

            // Direct Cloudflare Gateway Fallback
            try {
                $accountId = config('services.cloudflare.account_id');
                $gateway = config('services.cloudflare.ai_gateway');
                $token = config('services.cloudflare.ai_token');
                $model = config('services.cloudflare.ai_default_model', '@cf/meta/llama-3.1-8b-instruct');

                $res = \Illuminate\Support\Facades\Http::withToken($token)
                    ->timeout(12)
                    ->post("https://gateway.ai.cloudflare.com/v1/{$accountId}/{$gateway}/workers-ai/{$model}", [
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $message]
                        ]
                    ]);

                if ($res->successful()) {
                    return response()->json([
                        'success' => true,
                        'reply' => $res->json('result.response') ?? $res->json('response'),
                    ]);
                }
            } catch (\Throwable $ex) {}

            return response()->json([
                'success' => true,
                'reply' => "សូមអរគុណសម្រាប់សំណួររបស់អ្នក! ទាក់ទងនឹងមេរៀន {$course} សូមរំលឹកចំណុចគន្លឹះក្នុងស្លាយមេរៀន ឬសាកល្បងសួរម្តងទៀត។",
            ]);
        }
    }
}
