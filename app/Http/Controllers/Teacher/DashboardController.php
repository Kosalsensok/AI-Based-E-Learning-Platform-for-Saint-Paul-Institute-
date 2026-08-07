<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = $request->user()->id;

        $myCourses = Course::where('teacher_id', $teacherId)->get();
        $courseIds = $myCourses->pluck('id');

        $totalStudents = Enrollment::whereIn('course_id', $courseIds)->count() ?: 128;
        $activeStudents = (int) ($totalStudents * 0.75) ?: 95;

        $recentActivities = [
            [
                'id' => 1,
                'type' => 'video',
                'title' => 'Uploaded Video: 01_Introduction.mp4',
                'time' => '2 hours ago',
                'icon' => 'pi pi-video text-blue-500',
            ],
            [
                'id' => 2,
                'type' => 'enrollment',
                'title' => 'New student enrolled: Chan Dara',
                'time' => '3 hours ago',
                'icon' => 'pi pi-user-plus text-emerald-500',
            ],
            [
                'id' => 3,
                'type' => 'quiz',
                'title' => 'Quiz published: Module 2 Practice Quiz',
                'time' => '5 hours ago',
                'icon' => 'pi pi-check-square text-amber-500',
            ],
            [
                'id' => 4,
                'type' => 'discussion',
                'title' => 'Answer posted for question on C Loops',
                'time' => '1 day ago',
                'icon' => 'pi pi-comments text-purple-500',
            ],
        ];

        return Inertia::render('Teacher/Dashboard', [
            'teacherInfo' => [
                'name' => $request->user()->name,
                'title' => 'Instructor',
                'department' => 'IT & Networking',
                'status' => 'Online',
            ],
            'stats' => [
                'total_courses' => $myCourses->count() ?: 5,
                'total_students' => $totalStudents,
                'active_students' => $activeStudents,
                'pending_tasks' => 8,
            ],
            'recent_courses' => $myCourses->take(5),
            'recentActivities' => $recentActivities,
            'completionTrend' => [
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'data' => [45, 52, 58, 65, 72, 80, 85],
            ],
        ]);
    }
}
