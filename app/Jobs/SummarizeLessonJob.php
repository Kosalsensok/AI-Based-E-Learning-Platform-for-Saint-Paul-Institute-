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

class SummarizeLessonJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public int $courseId,
        public ?int $lessonId = null,
        public ?string $rawText = null,
        public ?int $aiContentId = null
    ) {}

    public function handle(CloudflareAIService $aiService): void
    {
        Log::info("SummarizeLessonJob started for Course #{$this->courseId}, Lesson #{$this->lessonId}");

        $course = Course::find($this->courseId);
        if (!$course) return;

        $lesson = $this->lessonId ? Lesson::find($this->lessonId) : null;
        $title = $lesson ? $lesson->title : $course->title;
        $content = $this->rawText ?: ($lesson ? ($lesson->content ?: $lesson->ai_summary ?: $lesson->title) : $course->description);

        $summary = $aiService->generateSummary($content ?: $title, $title);

        $payload = [
            'course_id' => $course->id,
            'lesson_id' => $lesson?->id,
            'type' => 'summary',
            'title' => "AI Summary: {$title}",
            'content_json' => $summary,
            'status' => 'draft',
        ];

        if ($this->aiContentId) {
            $aiContent = AiGeneratedContent::find($this->aiContentId);
            if ($aiContent) {
                $aiContent->update($payload);
            }
        } else {
            AiGeneratedContent::create($payload);
        }

        // Also update lesson->ai_summary field if lesson exists
        if ($lesson && !empty($summary['overview'])) {
            $lesson->update(['ai_summary' => $summary['overview']]);
        }

        Log::info("SummarizeLessonJob completed for {$title}");
    }
}
