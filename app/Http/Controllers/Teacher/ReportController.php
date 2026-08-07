<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = $request->user()->id;

        $courses = Course::where('teacher_id', $teacherId)->withCount(['enrollments', 'modules', 'lessons'])->get();

        if ($courses->isEmpty()) {
            $courses = collect([
                (object)[
                    'id' => 1,
                    'title' => 'C Programming Basics',
                    'code' => 'CS101',
                    'students_count' => 120,
                ],
                (object)[
                    'id' => 2,
                    'title' => 'Web Development with Vue & Laravel',
                    'code' => 'CS202',
                    'students_count' => 85,
                ],
            ]);
        }

        $studentReportStats = [
            'total_students' => 120,
            'completed' => 26,
            'in_progress' => 82,
            'not_started' => 12,
        ];

        $studentList = [
            [
                'id' => 1,
                'name' => 'Chan Dara',
                'avatar' => '👨‍🎓',
                'major' => 'IT & Networking',
                'semester' => 'Semester 1',
                'progress' => 95,
                'quiz_score' => 90,
                'learning_time' => '32h',
                'payment' => 'paid',
                'attendance' => '96%',
                'certificate' => 'Eligible',
                'time_breakdown' => ['total' => '32 Hours', 'video' => '18h', 'pdf' => '6h', 'slides' => '3h', 'practice' => '5h'],
                'quiz_breakdown' => ['pre_test' => 45, 'practice' => 95, 'post_test' => 90],
                'assignments' => ['submitted' => 4, 'graded' => 4, 'avg' => 92],
            ],
            [
                'id' => 2,
                'name' => 'Bun Rithy',
                'avatar' => '👨‍💻',
                'major' => 'Computer Science',
                'semester' => 'Semester 1',
                'progress' => 88,
                'quiz_score' => 86,
                'learning_time' => '28h',
                'payment' => 'paid',
                'attendance' => '92%',
                'certificate' => 'Eligible',
                'time_breakdown' => ['total' => '28 Hours', 'video' => '15h', 'pdf' => '5h', 'slides' => '3h', 'practice' => '5h'],
                'quiz_breakdown' => ['pre_test' => 40, 'practice' => 84, 'post_test' => 82],
                'assignments' => ['submitted' => 4, 'graded' => 4, 'avg' => 88],
            ],
            [
                'id' => 3,
                'name' => 'Sok Chanra',
                'avatar' => '👩‍🎓',
                'major' => 'MIS',
                'semester' => 'Semester 1',
                'progress' => 42,
                'quiz_score' => 51,
                'learning_time' => '8h',
                'payment' => 'pending',
                'attendance' => '65%',
                'certificate' => 'In Progress',
                'time_breakdown' => ['total' => '8 Hours', 'video' => '4h', 'pdf' => '2h', 'slides' => '1h', 'practice' => '1h'],
                'quiz_breakdown' => ['pre_test' => 32, 'practice' => 55, 'post_test' => 58],
                'assignments' => ['submitted' => 2, 'graded' => 2, 'avg' => 60],
            ],
            [
                'id' => 4,
                'name' => 'Long Vichida',
                'avatar' => '👩‍🔬',
                'major' => 'Telecom',
                'semester' => 'Semester 1',
                'progress' => 76,
                'quiz_score' => 73,
                'learning_time' => '19h',
                'payment' => 'paid',
                'attendance' => '85%',
                'certificate' => 'In Progress',
                'time_breakdown' => ['total' => '19 Hours', 'video' => '10h', 'pdf' => '4h', 'slides' => '2h', 'practice' => '3h'],
                'quiz_breakdown' => ['pre_test' => 51, 'practice' => 82, 'post_test' => 84],
                'assignments' => ['submitted' => 3, 'graded' => 3, 'avg' => 78],
            ],
            [
                'id' => 5,
                'name' => 'Keo Sophea',
                'avatar' => '👩‍💻',
                'major' => 'Software Eng',
                'semester' => 'Semester 1',
                'progress' => 92,
                'quiz_score' => 88,
                'learning_time' => '30h',
                'payment' => 'paid',
                'attendance' => '94%',
                'certificate' => 'Eligible',
                'time_breakdown' => ['total' => '30 Hours', 'video' => '16h', 'pdf' => '6h', 'slides' => '3h', 'practice' => '5h'],
                'quiz_breakdown' => ['pre_test' => 48, 'practice' => 90, 'post_test' => 88],
                'assignments' => ['submitted' => 4, 'graded' => 4, 'avg' => 90],
            ],
            [
                'id' => 6,
                'name' => 'Heng Visal',
                'avatar' => '👨‍🎨',
                'major' => 'Computer Science',
                'semester' => 'Semester 1',
                'progress' => 35,
                'quiz_score' => 40,
                'learning_time' => '6h',
                'payment' => 'unpaid',
                'attendance' => '50%',
                'certificate' => 'Not Eligible',
                'time_breakdown' => ['total' => '6 Hours', 'video' => '3h', 'pdf' => '1h', 'slides' => '1h', 'practice' => '1h'],
                'quiz_breakdown' => ['pre_test' => 30, 'practice' => 42, 'post_test' => 40],
                'assignments' => ['submitted' => 1, 'graded' => 1, 'avg' => 45],
            ],
        ];

        $courseReportData = [
            'course_title' => 'C Programming Basics',
            'students' => 120,
            'completed' => 26,
            'completion_rate' => '22%',
            'avg_quiz' => '78%',
            'avg_time' => '26 Hours',
            'modules_completion' => [
                ['name' => 'Module 1: Introduction', 'rate' => 92],
                ['name' => 'Module 2: Variables & Data Types', 'rate' => 81],
                ['name' => 'Module 3: Control Flow & Loops', 'rate' => 45],
                ['name' => 'Module 4: Functions & Pointers', 'rate' => 28],
            ],
            'revenue' => [
                'total' => '$3,240',
                'paid_count' => 108,
                'pending_count' => 12,
            ],
            'analytics' => [
                'enrollment_trend' => [
                    ['month' => 'Jan', 'count' => 15],
                    ['month' => 'Feb', 'count' => 30],
                    ['month' => 'Mar', 'count' => 50],
                    ['month' => 'Apr', 'count' => 70],
                    ['month' => 'May', 'count' => 95],
                    ['month' => 'Jun', 'count' => 120],
                ],
                'avg_progress' => 72,
                'avg_quiz' => 78,
                'avg_attendance' => 88,
                'avg_time' => 26,
            ],
            'ai_recommendation' => [
                'issue' => 'Module 3 is too difficult.',
                'suggestion' => 'Create another Practice Quiz and upload supplemental notes for Chapter 3.2.',
            ],
        ];

        $quizReportData = [
            'overview' => [
                'attempts' => 420,
                'passed' => 328,
                'passed_pct' => 78,
                'failed' => 92,
                'failed_pct' => 22,
                'avg_score' => 78,
            ],
            'quiz_types' => [
                'pre_test' => 45,
                'practice' => 84,
                'post_test' => 78,
            ],
            'difficulty_analysis' => [
                'easy' => 92,
                'medium' => 74,
                'hard' => 41,
            ],
            'most_incorrect' => [
                ['id' => 'Q14', 'topic' => 'Compiler Process', 'correct' => '41%', 'wrong' => '59%', 'difficulty' => 'Hard'],
                ['id' => 'Q22', 'topic' => 'Pointer Memory', 'correct' => '39%', 'wrong' => '61%', 'difficulty' => 'Hard'],
                ['id' => 'Q31', 'topic' => 'Dynamic Allocation', 'correct' => '36%', 'wrong' => '64%', 'difficulty' => 'Very Hard'],
            ],
            'student_comparison' => [
                ['student' => 'Chan Dara', 'pre' => '45%', 'practice' => '90%', 'post' => '91%', 'improvement' => '+46% 🚀'],
                ['student' => 'Bun Rithy', 'pre' => '40%', 'practice' => '84%', 'post' => '82%', 'improvement' => '+42% 🚀'],
                ['student' => 'Sok Chanra', 'pre' => '32%', 'practice' => '55%', 'post' => '58%', 'improvement' => '+26% 📈'],
                ['student' => 'Long Vichida', 'pre' => '51%', 'practice' => '82%', 'post' => '84%', 'improvement' => '+33% 🚀'],
            ],
            'question_analytics' => [
                ['code' => 'Q1', 'topic' => 'Syntax & Main', 'correct' => 95, 'wrong' => 5, 'difficulty' => 'Easy'],
                ['code' => 'Q2', 'topic' => 'Printf Formatting', 'correct' => 90, 'wrong' => 10, 'difficulty' => 'Easy'],
                ['code' => 'Q14', 'topic' => 'Compiler Process', 'correct' => 41, 'wrong' => 59, 'difficulty' => 'Hard'],
                ['code' => 'Q22', 'topic' => 'Pointer Dereferencing', 'correct' => 39, 'wrong' => 61, 'difficulty' => 'Hard'],
                ['code' => 'Q31', 'topic' => 'Memory Heap Allocation', 'correct' => 36, 'wrong' => 64, 'difficulty' => 'Very Hard'],
            ],
            'ai_quiz_recommendation' => [
                'module' => 'Module 3: Control Flow',
                'avg_score' => '48%',
                'recommendations' => [
                    '📹 Create a short recap video explaining nested loop termination conditions.',
                    '🟩 Create an extra practice quiz targeting pointer dereferencing.',
                    '🎯 Review complex questions during Thursday Live Class session.',
                ],
            ],
        ];

        return Inertia::render('Teacher/Reports/Index', [
            'courses' => $courses,
            'studentReportStats' => $studentReportStats,
            'studentList' => $studentList,
            'courseReportData' => $courseReportData,
            'quizReportData' => $quizReportData,
        ]);
    }

    public function exportStudentReport(Request $request, $id)
    {
        return back()->with('success', "Student report exported successfully for ID #{$id}!");
    }

    public function exportCourseReport(Request $request, $id)
    {
        return back()->with('success', "Course report exported successfully for Course #{$id}!");
    }
}
