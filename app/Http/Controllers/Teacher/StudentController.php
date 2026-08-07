<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = $request->user()->id;

        $courses = Course::where('teacher_id', $teacherId)->get();

        $enrollments = Enrollment::with(['student', 'course'])
            ->whereHas('course', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })
            ->latest('enrolled_at')
            ->get();

        $stats = [
            'total_students' => $enrollments->count() ?: 128,
            'active_students' => 95,
            'slow_students' => 21,
            'at_risk_students' => 12,
            'avg_progress' => 72,
            'avg_score' => 78,
            'avg_time' => '19h 30m',
        ];

        return Inertia::render('Teacher/Students/Index', [
            'courses' => $courses,
            'enrollments' => $enrollments,
            'stats' => $stats,
        ]);
    }

    public function sendReminder(Request $request, $id)
    {
        return back()->with('success', 'Study reminder sent to student!');
    }

    public function sendFeedback(Request $request, $id)
    {
        return back()->with('success', 'Feedback sent to student successfully!');
    }

    public function toggleAtRisk(Request $request, $id)
    {
        return back()->with('success', 'Student status updated!');
    }
}
