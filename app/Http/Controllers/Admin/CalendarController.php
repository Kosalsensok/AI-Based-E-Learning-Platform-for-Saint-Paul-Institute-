<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function index()
    {
        $liveClasses = Course::where('learning_mode', 'instructor_led')
            ->with('teacher')
            ->get();

        $upcomingQuizzes = Quiz::with('course')->get();

        return Inertia::render('Admin/Calendar/Index', [
            'liveClasses' => $liveClasses,
            'upcomingQuizzes' => $upcomingQuizzes,
        ]);
    }
}
