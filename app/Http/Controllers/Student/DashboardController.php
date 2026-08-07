<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $enrollments = Enrollment::where('student_id', $request->user()->id)
            ->with(['course.teacher'])
            ->get();

        return Inertia::render('Student/Dashboard', [
            'enrollments' => $enrollments,
        ]);
    }
}
