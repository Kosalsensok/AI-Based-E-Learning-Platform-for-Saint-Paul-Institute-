<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Major;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Courses/Index', [
            'courses' => Course::with(['teacher', 'major'])->latest()->get(),
            'teachers' => User::whereIn('role', ['teacher', 'admin'])->get(['id', 'name']),
            'majors' => Major::where('is_active', true)->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:users,id',
            'major_id' => 'nullable|exists:majors,id',
            'learning_mode' => 'required|in:self_paced,instructor_led,hybrid',
            'is_paid' => 'required|boolean',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,pending_approval,published,rejected',
        ]);

        if (!$data['is_paid']) {
            $data['price'] = 0;
        }

        Course::create($data);

        return back()->with('success', 'Course created successfully!');
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:users,id',
            'major_id' => 'nullable|exists:majors,id',
            'learning_mode' => 'required|in:self_paced,instructor_led,hybrid',
            'is_paid' => 'required|boolean',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,pending_approval,published,rejected',
        ]);

        if (!$data['is_paid']) {
            $data['price'] = 0;
        }

        $course->update($data);

        return back()->with('success', 'Course updated successfully!');
    }

    public function approve(Course $course)
    {
        $course->update(['status' => 'published']);
        return back()->with('success', 'Course approved & published!');
    }

    public function reject(Course $course)
    {
        $course->update(['status' => 'rejected']);
        return back()->with('success', 'Course rejected');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return back()->with('success', 'Course deleted successfully');
    }
}
