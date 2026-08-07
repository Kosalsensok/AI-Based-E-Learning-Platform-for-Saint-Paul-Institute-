<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'live-class');
        return Inertia::render('Student/Calendar/Index', [
            'activeTab' => $tab,
        ]);
    }

    public function liveClassSchedule(Request $request)
    {
        return Inertia::render('Student/Calendar/LiveClassSchedule');
    }

    public function upcomingQuiz(Request $request)
    {
        return Inertia::render('Student/Calendar/UpcomingQuiz');
    }

    public function assignmentDue(Request $request)
    {
        return Inertia::render('Student/Calendar/AssignmentDue');
    }
}
