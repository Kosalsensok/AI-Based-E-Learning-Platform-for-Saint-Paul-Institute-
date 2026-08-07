<?php

namespace App\Services;

use App\Models\Quiz;

class GradingService
{
    public function grade(Quiz $quiz, array $answers): array
    {
        $quiz->loadMissing('questions');
        $totalPoints = 0; $earnedPoints = 0; $breakdown = [];

        foreach ($quiz->questions as $q) {
            $totalPoints += $q->points;
            $userAnswer = $answers[$q->id] ?? null;
            $correct = false;

            match ($q->type) {
                'mcq', 'true_false' => $correct = $userAnswer === $q->correct_answer,
                'fill_blank' => $correct = strtolower(trim($userAnswer ?? '')) ===
                    strtolower(trim($q->correct_answer)),
                'essay' => $correct = null,  // teacher grades manually
                default => $correct = false,
            };

            if ($correct) $earnedPoints += $q->points;
            $breakdown[$q->id] = ['correct' => $correct, 'points' => $q->points];
        }

        $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
        $passed = $score >= $quiz->passing_score;

        return [$score, $passed, $breakdown];
    }
}
