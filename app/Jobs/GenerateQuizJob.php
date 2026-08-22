<?php

namespace App\Jobs;

use App\Models\AiGeneratedContent;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\CloudflareAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateQuizJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public int $courseId,
        public ?int $lessonId = null,
        public ?string $topic = null,
        public int $numQuestions = 4,
        public ?int $aiContentId = null
    ) {}

    public function handle(CloudflareAIService $aiService): void
    {
        Log::info("GenerateQuizJob started for Course #{$this->courseId}, Lesson #{$this->lessonId}");

        $course = Course::find($this->courseId);
        if (!$course) return;

        $lesson = $this->lessonId ? Lesson::find($this->lessonId) : null;
        $topicTitle = $this->topic ?: ($lesson ? $lesson->title : $course->title);
        $context = $lesson ? ($lesson->content ?: $lesson->ai_summary ?: $lesson->title) : $course->description;
        $majorCode = $course->major ? ($course->major->code ?? $course->major->name ?? 'it') : 'it';

        $questions = $aiService->generateQuiz($context ?: $topicTitle, $topicTitle, $this->numQuestions, (string) $majorCode);

        $payload = [
            'course_id' => $course->id,
            'lesson_id' => $lesson?->id,
            'type' => 'quiz',
            'title' => "AI Generated Quiz: {$topicTitle}",
            'content_json' => [
                'questions' => $questions,
                'topic' => $topicTitle,
                'model' => config('services.cloudflare.default_model', '@cf/meta/llama-3.1-8b-instruct'),
                'generated_at' => now()->toIso8601String(),
            ],
            'status' => 'draft',
        ];

        if ($this->aiContentId) {
            $aiContent = AiGeneratedContent::find($this->aiContentId);
            if ($aiContent) {
                $aiContent->update($payload);
                Log::info("GenerateQuizJob updated AiGeneratedContent #{$aiContent->id}");
                return;
            }
        }

        $created = AiGeneratedContent::create($payload);
        Log::info("GenerateQuizJob created AiGeneratedContent #{$created->id}");
    }
}
