<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class OfflineController extends Controller
{
    public function package(Request $request, Course $course)
    {
        // ត្រូវ Enroll + Active ទើប download បាន
        abort_unless(Enrollment::where('student_id', $request->user()->id)
            ->where('course_id', $course->id)->where('status', 'active')->exists(), 403);

        return response()->json([
            'course'  => $course->only('id', 'title', 'learning_mode'),
            'modules' => $course->modules()->with(['lessons' => fn ($q) => $q->where('downloadable', true)])->get(),
            'quizzes' => $course->quizzes()->where('status', 'published')
                ->with('questions:id,quiz_id,type,question,options,points')->get(),
            'generated_at' => now()->timestamp,
        ]);
    }
}
