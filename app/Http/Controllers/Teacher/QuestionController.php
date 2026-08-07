<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function store(Request $request, Quiz $quiz)
    {
        if ($quiz->course->teacher_id !== $request->user()->id) abort(403);

        $data = $request->validate([
            'type' => 'required|in:mcq,true_false,fill_blank,essay',
            'question' => 'required|string',
            'options' => 'nullable|array',
            'correct_answer' => 'nullable',
            'points' => 'required|integer|min:1',
        ]);

        $quiz->questions()->create($data);
        return back()->with('success', 'Question added successfully');
    }

    public function update(Request $request, Question $question)
    {
        if ($question->quiz->course->teacher_id !== $request->user()->id) abort(403);

        $data = $request->validate([
            'type' => 'required|in:mcq,true_false,fill_blank,essay',
            'question' => 'required|string',
            'options' => 'nullable|array',
            'correct_answer' => 'nullable',
            'points' => 'required|integer|min:1',
        ]);

        $question->update($data);
        return back()->with('success', 'Question updated successfully');
    }

    public function destroy(Question $question)
    {
        if ($question->quiz->course->teacher_id !== auth()->id()) abort(403);
        $question->delete();
        return back()->with('success', 'Question deleted successfully');
    }
}
