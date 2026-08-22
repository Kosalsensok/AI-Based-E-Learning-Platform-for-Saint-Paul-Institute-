<?php

namespace App\Jobs;

use App\Models\AiRecommendation;
use App\Models\Lesson;
use App\Models\User;
use App\Services\CloudflareAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GenerateRecommendationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public int $userId,
        public ?int $lessonId = null,
        public ?int $quizScore = null
    ) {}

    public function handle(CloudflareAIService $aiService): void
    {
        Log::info("GenerateRecommendationJob started for User #{$this->userId}");

        $user = User::with('major')->find($this->userId);
        if (!$user) return;

        $lesson = $this->lessonId ? Lesson::find($this->lessonId) : null;
        $lessonTitle = $lesson ? $lesson->title : 'Academic Foundation';
        $majorName = $user->major ? ($user->major->name ?? $user->major->code ?? 'Information Technology') : 'Information Technology';
        $score = $this->quizScore ?? 80;

        $rec = $aiService->generateRecommendation($majorName, $lessonTitle, null, $score);

        // Store in cache for high-speed front-end consumption
        $cacheKey = "ai_rec_{$user->id}_" . ($lesson ? $lesson->id : 'latest') . "_{$score}";
        Cache::put($cacheKey, $rec, 86400);

        // Persist structured recommendation in AiRecommendation table
        $reason = $rec['next_topic'] ?? ($rec['practice_project'] ?? "Recommended learning path for {$majorName}");

        AiRecommendation::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson?->id,
                'type' => 'next_steps',
            ],
            [
                'reason' => (string) $reason,
                'is_dismissed' => false,
                'created_at' => now(),
            ]
        );

        Log::info("GenerateRecommendationJob completed for User #{$user->id}");
    }
}
