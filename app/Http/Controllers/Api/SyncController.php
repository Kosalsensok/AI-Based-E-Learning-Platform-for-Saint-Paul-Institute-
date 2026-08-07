<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\GradingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function progress(Request $request)
    {
        $data = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'percent'   => 'required|integer|min:0|max:100',
            'seconds'   => 'required|integer|min:0',
            'client_at' => 'required|integer',
        ]);

        $existing = LessonProgress::firstOrNew([
            'user_id' => $request->user()->id, 'lesson_id' => $data['lesson_id'],
        ]);

        // Conflict resolution: យក percent ធំបំផុត
        $existing->percent = max($existing->percent ?? 0, $data['percent']);
        $existing->seconds_watched = max($existing->seconds_watched ?? 0, $data['seconds']);
        $existing->client_updated_at = Carbon::createFromTimestampMs($data['client_at']);
        if ($existing->percent >= 90 && !$existing->completed_at) {
            $existing->completed_at = now();
        }
        $existing->save();

        return response()->json(['synced' => true]);
    }

    public function quizAttempt(Request $request)
    {
        $data = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'answers' => 'required|array',
            'client_uuid' => 'required|uuid',
            'started_at' => 'required|integer',
        ]);

        // R6: dedupe — បើ sync ម្តងទៀត មិនបង្កើតស្ទួន
        if (QuizAttempt::where('client_uuid', $data['client_uuid'])->exists()) {
            return response()->json(['synced' => true, 'duplicate' => true]);
        }

        // Server-side grading (correct_answer នៅ server តែប៉ុណ្ណោះ)
        $quiz = Quiz::with('questions')->findOrFail($data['quiz_id']);
        [$score, $passed] = app(GradingService::class)->grade($quiz, $data['answers']);

        QuizAttempt::create([
            'user_id' => $request->user()->id,
            'quiz_id' => $quiz->id,
            'answers' => $data['answers'],
            'score' => $score,
            'passed' => $passed,
            'client_uuid' => $data['client_uuid'],
            'attempt_number' => QuizAttempt::where('user_id', $request->user()->id)
                ->where('quiz_id', $quiz->id)->count() + 1,
            'started_at' => Carbon::createFromTimestampMs($data['started_at']),
            'submitted_at' => now(),
        ]);

        return response()->json(['synced' => true, 'score' => $score, 'passed' => $passed]);
    }
}
