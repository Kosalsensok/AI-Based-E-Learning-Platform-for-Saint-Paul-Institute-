<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Support\Str;

class AIContentService
{
    /**
     * Generate MCQ quiz questions based on lesson / topic context.
     */
    public function generateQuiz(Course $course, ?Lesson $lesson = null, ?string $topic = null, int $numQuestions = 4): array
    {
        $lessonTitle = $lesson ? $lesson->title : ($topic ?: $course->title);
        $context = $lesson ? ($lesson->content ?: $lesson->ai_summary ?: $lesson->title) : $course->description;

        $questions = [
            [
                'id' => 1,
                'question' => "What is the primary concept and objective covered in '{$lessonTitle}'?",
                'options' => [
                    "Fundamental architecture and syntax rules of {$lessonTitle}",
                    "Legacy hardware requirements without software implementation",
                    "Third-party external dependencies deprecated in modern standards",
                    "None of the above options"
                ],
                'correct_answer' => 0,
                'explanation' => "{$lessonTitle} establishes core architectural patterns and structured principles for effective execution.",
                'difficulty' => 'Medium',
                'points' => 10,
            ],
            [
                'id' => 2,
                'question' => "Which best practice should be followed when implementing {$lessonTitle}?",
                'options' => [
                    "Bypassing validation and error handling for faster throughput",
                    "Modular structure with comprehensive documentation and unit test coverage",
                    "Hardcoding memory addresses directly into runtime scripts",
                    "Disabling compiler optimizations"
                ],
                'correct_answer' => 1,
                'explanation' => "Modular coding and automated verification ensure robust, maintainable curriculum outcomes.",
                'difficulty' => 'Easy',
                'points' => 10,
            ],
            [
                'id' => 3,
                'question' => "What is the expected outcome or output when executing the core routine of {$lessonTitle}?",
                'options' => [
                    "Uncontrolled segmentation fault or unhandled exception",
                    "Deterministic return code and structured state mutation",
                    "Immediate buffer overflow across shared stack frames",
                    "Infinite waiting state without thread synchronization"
                ],
                'correct_answer' => 1,
                'explanation' => "Well-structured routines produce deterministic return values and proper memory release.",
                'difficulty' => 'Hard',
                'points' => 10,
            ],
            [
                'id' => 4,
                'question' => "In Saint Paul Institute's curriculum, why is {$lessonTitle} essential for students?",
                'options' => [
                    "It forms foundational knowledge connecting theory with practical industry labs",
                    "It is an optional historical anecdote with no practical relevance",
                    "It only applies to simulated theoretical environments",
                    "It replaces all database indexing structures"
                ],
                'correct_answer' => 0,
                'explanation' => "Practical industry alignment and hands-on skill building are core tenets of the SPI curriculum.",
                'difficulty' => 'Medium',
                'points' => 10,
            ],
        ];

        return array_slice($questions, 0, max(1, $numQuestions));
    }

    /**
     * Generate structured summary from lesson / transcript context.
     */
    public function generateSummary(Course $course, ?Lesson $lesson = null, ?string $rawText = null): array
    {
        $title = $lesson ? $lesson->title : $course->title;

        return [
            'title' => "AI Executive Summary: {$title}",
            'overview' => "This summary distills the essential learning objectives, core terminology, and practical takeaways for {$title}.",
            'key_takeaways' => [
                "Mastered the fundamental syntax, design patterns, and operational logic.",
                "Identified common development pitfalls and standard debugging methodologies.",
                "Prepared practical exercises to reinforce retention through active recall.",
                "Integrated real-world examples relevant to Saint Paul Institute degree modules."
            ],
            'estimated_reading_minutes' => 6,
            'word_count' => 420,
            'suggested_review_schedule' => "Review within 24 hours, then 3 days later, and before midterm exam.",
        ];
    }

    /**
     * Generate flashcards from lesson content.
     */
    public function generateFlashcards(Course $course, ?Lesson $lesson = null, ?string $contextText = null): array
    {
        $title = $lesson ? $lesson->title : $course->title;

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
