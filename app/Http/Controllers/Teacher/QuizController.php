<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuizController extends Controller
{
    public function globalIndex(Request $request)
    {
        $quizzes = Quiz::whereHas('course', function($q) use ($request) {
            $q->where('teacher_id', $request->user()->id);
        })->with('course')->withCount('questions')->get();

        $courses = Course::where('teacher_id', $request->user()->id)->get();

        return Inertia::render('Teacher/Quizzes/Index', [
            'quizzes' => $quizzes,
            'courses' => $courses
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:pre_test,practice,post_test,assignment',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'max_attempts' => 'required|integer|min:1',
            'course_id' => 'required|exists:courses,id'
        ]);

        $course = Course::findOrFail($data['course_id']);
        if ($course->teacher_id !== $request->user()->id) abort(403);

        $course->quizzes()->create($data);
        return back()->with('success', 'Quiz created successfully');
    }

    public function update(Request $request, Quiz $quiz)
    {
        if ($quiz->course->teacher_id !== $request->user()->id) abort(403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:pre_test,practice,post_test,assignment',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'max_attempts' => 'required|integer|min:1',
        ]);

        $quiz->update($data);
        return back()->with('success', 'Quiz updated successfully');
    }

    public function destroy(Quiz $quiz)
    {
        if ($quiz->course->teacher_id !== auth()->id()) abort(403);
        $quiz->delete();
        return back()->with('success', 'Quiz deleted successfully');
    }

    public function show(Quiz $quiz)
    {
        if ($quiz->course->teacher_id !== auth()->id()) abort(403);

        $quiz->load('questions');
        return Inertia::render('Teacher/Quizzes/Show', [
            'quiz' => $quiz
        ]);
    }
}
