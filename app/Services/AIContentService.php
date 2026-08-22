<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Support\Str;

class AIContentService
{
    protected CloudflareAIService $cfAi;

    public function __construct(?CloudflareAIService $cfAi = null)
    {
        $this->cfAi = $cfAi ?: app(CloudflareAIService::class);
    }

    /**
     * Generate MCQ quiz questions based on lesson / topic context using Cloudflare Workers AI.
     */
    public function generateQuiz(Course $course, ?Lesson $lesson = null, ?string $topic = null, int $numQuestions = 4): array
    {
        $lessonTitle = $lesson ? $lesson->title : ($topic ?: $course->title);
        $context = $lesson ? ($lesson->content ?: $lesson->ai_summary ?: $lesson->title) : $course->description;
        $majorCode = $course->major ? ($course->major->code ?? $course->major->name ?? 'it') : 'it';

        return $this->cfAi->generateQuiz($context ?: $lessonTitle, $lessonTitle, $numQuestions, (string) $majorCode);
    }

    /**
     * Generate structured summary from lesson / transcript context using Cloudflare Workers AI.
     */
    public function generateSummary(Course $course, ?Lesson $lesson = null, ?string $rawText = null): array
    {
        $title = $lesson ? $lesson->title : $course->title;
        $context = $rawText ?: ($lesson ? ($lesson->content ?: $lesson->ai_summary ?: $lesson->title) : $course->description);

        return $this->cfAi->generateSummary($context ?: $title, $title);
    }

    /**
     * Generate flashcards from lesson content.
     */
    public function generateFlashcards(Course $course, ?Lesson $lesson = null, ?string $contextText = null): array
    {
        $title = $lesson ? $lesson->title : $course->title;
        $context = $contextText ?: ($lesson ? ($lesson->content ?: $lesson->ai_summary) : $course->description);

        $prompt = "Generate 4 key term study flashcards for '{$title}' in Saint Paul Institute courses. Context: " . Str::limit((string)$context, 1000) . "
Output JSON format:
[
  {\"id\": 1, \"front\": \"Term/Concept\", \"back\": \"Definition and usage\", \"tag\": \"Core/Syntax/Pattern\", \"mastery_level\": \"Core/Intermediate/Advanced\"}
]";

        $res = $this->cfAi->runModel(config('services.cloudflare.default_model', '@cf/meta/llama-3.1-8b-instruct'), [
            'messages' => [
                ['role' => 'system', 'content' => 'You are a flashcard generator. Output only valid JSON array.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.3,
            'max_tokens' => 800
        ]);

        $raw = $res ? ($res['result']['choices'][0]['message']['content'] ?? $res['result']['response'] ?? null) : null;
        if ($raw) {
            $clean = trim(preg_replace('/^```json\s*|^```\s*|\s*```$/i', '', $raw));
            $parsed = json_decode($clean, true);
            if (is_array($parsed) && count($parsed) > 0) {
                return $parsed;
            }
        }

        return [
            [
                'id' => 1,
                'front' => "What is the primary definition of '{$title}'?",
                'back' => "A core component of the {$course->title} syllabus that equips students with fundamental and applied competencies.",
                'tag' => 'Definition',
                'mastery_level' => 'Core',
            ],
            [
                'id' => 2,
                'front' => "Key syntax or architectural pattern in {$title}?",
                'back' => "Structured separation of concerns, strict type safety, and clean API boundaries.",
                'tag' => 'Syntax & Pattern',
                'mastery_level' => 'Intermediate',
            ],
            [
                'id' => 3,
                'front' => "Common error to watch out for during {$title} lab practice?",
                'back' => "Null references, missing semicolons, off-by-one loop boundaries, and unclosed network sockets.",
                'tag' => 'Debugging',
                'mastery_level' => 'Troubleshooting',
            ],
            [
                'id' => 4,
                'front' => "Real-world industry application of {$title}?",
                'back' => "Enterprise backend systems, network orchestration, and cloud infrastructure pipelines.",
                'tag' => 'Industry Use',
                'mastery_level' => 'Advanced',
            ]
        ];
    }
}
