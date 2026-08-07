<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LearningController extends Controller
{
    public function content(Request $request)
    {
        $tab = $request->query('tab', 'videos');

        return Inertia::render('Student/LearningContent/Index', [
            'activeTab' => $tab,
        ]);
    }

    public function videos(Request $request)
    {
        return Inertia::render('Student/LearningContent/Videos');
    }

    public function pdfs(Request $request)
    {
        return Inertia::render('Student/LearningContent/Pdfs');
    }

    public function slides(Request $request)
    {
        return Inertia::render('Student/LearningContent/Slides');
    }

    public function notes(Request $request)
    {
        return Inertia::render('Student/LearningContent/NotesDownloads');
    }

    public function links(Request $request)
    {
        return Inertia::render('Student/LearningContent/UrlResources');
    }

    public function show(Request $request, Course $course)
    {
        $enrollment = Enrollment::where('student_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        abort_unless($enrollment->status === 'active', 402, 'Payment required');

        $lessonIds = $course->lessons()->pluck('id');

        $progress = LessonProgress::where('user_id', $request->user()->id)
            ->whereIn('lesson_id', $lessonIds)
            ->get()->keyBy('lesson_id');

        return Inertia::render('Student/Learning/Show', [
            'course'     => $course->load('modules.lessons'),
            'progress'   => $progress,
            'enrollment' => $enrollment,
        ]);
    }

    public function updateProgress(Request $request, Lesson $lesson)
    {
        $data = $request->validate([
            'percent' => 'required|integer|min:0|max:100',
            'seconds' => 'required|integer|min:0',
        ]);

        $prog = LessonProgress::firstOrNew([
            'user_id'   => $request->user()->id,
            'lesson_id' => $lesson->id,
        ]);

        $prog->percent = max($prog->percent ?? 0, $data['percent']);
        $prog->seconds_watched = max($prog->seconds_watched ?? 0, $data['seconds']);
        if ($data['percent'] >= 90 && !$prog->completed_at) {
            $prog->completed_at = now();
        }
        $prog->save();

        return response()->json(['ok' => true, 'percent' => $prog->percent]);
    }
}
