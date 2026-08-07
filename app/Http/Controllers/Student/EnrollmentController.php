<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $status = $course->is_paid ? 'pending_payment' : 'active';

        $enrollment = Enrollment::firstOrCreate(
            ['student_id' => $request->user()->id, 'course_id' => $course->id],
            ['status' => $status, 'enrolled_at' => $status === 'active' ? now() : null]
        );

        return back()->with('success', 'Enrolled successfully');
    }
}
