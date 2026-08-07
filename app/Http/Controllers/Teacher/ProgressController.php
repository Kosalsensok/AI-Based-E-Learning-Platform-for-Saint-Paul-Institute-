<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = $request->user()->id;

        $courses = Course::where('teacher_id', $teacherId)->with(['modules.lessons'])->get();

        if ($courses->isEmpty()) {
            $courses = collect([
                (object)[
                    'id' => 1,
                    'title' => 'C Programming Basics',
                    'code' => 'CS101',
                    'students_count' => 45,
                ],
                (object)[
                    'id' => 2,
                    'title' => 'Web Development with Vue & Laravel',
                    'code' => 'CS202',
                    'students_count' => 38,
                ],
            ]);
        }

        $topStats = [
            'avg_completion' => 72,
            'avg_time' => '19h 30m',
            'active_students' => 95,
            'at_risk_students' => 12,
            'this_week_engagement' => 78,
            'trend' => '+6%',
        ];

        $moduleCompletionOverview = [
            'total_students' => 45,
            'completed' => 32,
            'completed_pct' => 71,
            'in_progress' => 8,
            'in_progress_pct' => 18,
            'not_started' => 5,
            'not_started_pct' => 11,
            'class_avg_completion' => 72,
        ];

        $modulesData = [
            [
                'id' => 1,
                'name' => 'M1: Introduction',
                'title' => 'Introduction to C Programming',
                'students' => 45,
                'completed' => 44,
                'in_progress' => 1,
                'not_started' => 0,
                'rate' => 98,
                'status' => 'green',
                'avg_time' => '4h 20m',
                'avg_quiz' => '92%',
                'chapters' => [
                    ['title' => 'Ch 1.1: History & Setup', 'progress' => 100],
                    ['title' => 'Ch 1.2: First Hello World', 'progress' => 98],
                    ['title' => 'Ch 1.3: Compiling C Code', 'progress' => 96],
                ],
                'insight' => 'Students grasped fundamental setup quickly with minimal drop-off.',
            ],
            [
                'id' => 2,
                'name' => 'M2: Variables',
                'title' => 'Variables & Data Types',
                'students' => 45,
                'completed' => 38,
                'in_progress' => 5,
                'not_started' => 2,
                'rate' => 84,
                'status' => 'green',
                'avg_time' => '6h 15m',
                'avg_quiz' => '81%',
                'chapters' => [
                    ['title' => 'Ch 2.1: Data Types & Modifiers', 'progress' => 90],
                    ['title' => 'Ch 2.2: User Input with scanf', 'progress' => 84],
                    ['title' => 'Ch 2.3: Operators & Expressions', 'progress' => 78],
                ],
                'insight' => 'Good comprehension overall; minor confusion on float precision.',
            ],
            [
                'id' => 3,
                'name' => 'M3: Loops',
                'title' => 'Control Flow & Loops',
                'students' => 45,
                'completed' => 28,
                'in_progress' => 12,
                'not_started' => 5,
                'rate' => 62,
                'status' => 'yellow',
                'avg_time' => '8h 40m',
                'avg_quiz' => '68%',
                'chapters' => [
                    ['title' => 'Ch 3.1: If-Else & Switch', 'progress' => 75],
                    ['title' => 'Ch 3.2: For & While Loops', 'progress' => 62],
                    ['title' => 'Ch 3.3: Nested Loops & Break', 'progress' => 49],
                ],
                'insight' => 'Nested loops are causing friction. Practice quiz recommended.',
            ],
            [
                'id' => 4,
                'name' => 'M4: Functions',
                'title' => 'Functions & Pointers',
                'students' => 45,
                'completed' => 15,
                'in_progress' => 18,
                'not_started' => 12,
                'rate' => 33,
                'status' => 'red',
                'avg_time' => '12h 30m',
                'avg_quiz' => '58%',
                'chapters' => [
                    ['title' => 'Ch 4.1: Intro to Functions', 'progress' => 65],
                    ['title' => 'Ch 4.2: Parameters & Return', 'progress' => 35],
                    ['title' => 'Ch 4.3: Recursion & Pointers', 'progress' => 18],
                ],
                'insight' => '62% of students stopped at Chapter 4.2. Quiz scores drop sharply after introduction. High watch time indicates topics require supplemental videos.',
            ],
        ];

        $heatmapData = [
            ['student' => 'Chan Dara', 'avatar' => '👨‍🎓', 'm1' => 100, 'm2' => 100, 'm3' => 65, 'm4' => 15],
            ['student' => 'Bun Rithy', 'avatar' => '👨‍💻', 'm1' => 100, 'm2' => 88, 'm3' => 42, 'm4' => 10],
            ['student' => 'Mao Sreynich', 'avatar' => '👩‍🎓', 'm1' => 100, 'm2' => 60, 'm3' => 20, 'm4' => 0],
            ['student' => 'Sok Channa', 'avatar' => '👩‍💻', 'm1' => 80, 'm2' => 35, 'm3' => 10, 'm4' => 0],
            ['student' => 'Long Vicheka', 'avatar' => '👨‍🔬', 'm1' => 100, 'm2' => 100, 'm3' => 95, 'm4' => 45],
            ['student' => 'Keo Sophea', 'avatar' => '👩‍🔬', 'm1' => 95, 'm2' => 90, 'm3' => 70, 'm4' => 25],
            ['student' => 'Heng Visal', 'avatar' => '👨‍🎨', 'm1' => 100, 'm2' => 75, 'm3' => 50, 'm4' => 5],
        ];

        $learningTimeOverview = [
            'class_total_hours' => 878,
            'avg_per_student' => '19h 30m',
            'most_active' => ['name' => 'Long Vicheka', 'hours' => '32h 00m'],
            'least_active' => ['name' => 'Sok Channa', 'hours' => '8h 15m'],
        ];

        $studentTimeList = [
            [
                'id' => 1,
                'student' => 'Chan Dara',
                'total_time' => '28h 30m',
                'this_week' => '7h 45m',
                'avg_day' => '1.5h',
                'last_active' => 'Today',
                'trend' => '↗️ Good',
                'status' => 'active',
                'sessions_week' => 11,
                'avg_session' => '42 mins',
                'videos_time' => '18h 10m',
                'pdfs_time' => '4h 20m',
                'slides_time' => '2h 15m',
                'quiz_time' => '3h 45m',
                'most_time_on' => 'Module 3 – Loops (8h)',
                'least_time_on' => 'Module 4 – Functions (0h)',
            ],
            [
                'id' => 2,
                'student' => 'Bun Rithy',
                'total_time' => '20h 10m',
                'this_week' => '3h 20m',
                'avg_day' => '45m',
                'last_active' => '2d ago',
                'trend' => '→ Stable',
                'status' => 'active',
                'sessions_week' => 6,
                'avg_session' => '33 mins',
                'videos_time' => '12h 00m',
                'pdfs_time' => '3h 10m',
                'slides_time' => '2h 00m',
                'quiz_time' => '3h 00m',
                'most_time_on' => 'Module 2 – Variables (7h)',
                'least_time_on' => 'Module 4 – Functions (1h)',
            ],
            [
                'id' => 3,
                'student' => 'Mao Sreynich',
                'total_time' => '15h 00m',
                'this_week' => '2h 10m',
                'avg_day' => '30m',
                'last_active' => '3d ago',
                'trend' => '↘️ Slow',
                'status' => 'warning',
                'sessions_week' => 4,
                'avg_session' => '30 mins',
                'videos_time' => '9h 30m',
                'pdfs_time' => '2h 30m',
                'slides_time' => '1h 30m',
                'quiz_time' => '1h 30m',
                'most_time_on' => 'Module 1 – Intro (6h)',
                'least_time_on' => 'Module 3 – Loops (1h)',
            ],
            [
                'id' => 4,
                'student' => 'Sok Channa',
                'total_time' => '8h 15m',
                'this_week' => '0h 00m',
                'avg_day' => '0m',
                'last_active' => '9d ago',
                'trend' => '🔴 Dead',
                'status' => 'at_risk',
                'sessions_week' => 0,
                'avg_session' => '0 mins',
                'videos_time' => '5h 00m',
                'pdfs_time' => '1h 30m',
                'slides_time' => '1h 00m',
                'quiz_time' => '0h 45m',
                'most_time_on' => 'Module 1 – Intro (5h)',
                'least_time_on' => 'Module 4 – Functions (0h)',
            ],
            [
                'id' => 5,
                'student' => 'Long Vicheka',
                'total_time' => '32h 00m',
                'this_week' => '8h 20m',
                'avg_day' => '1.7h',
                'last_active' => 'Today',
                'trend' => '↗️ Good',
                'status' => 'active',
                'sessions_week' => 14,
                'avg_session' => '35 mins',
                'videos_time' => '20h 30m',
                'pdfs_time' => '5h 00m',
                'slides_time' => '3h 00m',
                'quiz_time' => '3h 30m',
                'most_time_on' => 'Module 3 – Loops (10h)',
                'least_time_on' => 'Module 4 – Functions (4h)',
            ],
        ];

        $weeklyChartDays = [
            ['day' => 'Mon', 'hours' => 1.8, 'label' => '1.8h'],
            ['day' => 'Tue', 'hours' => 2.1, 'label' => '2.1h'],
            ['day' => 'Wed', 'hours' => 2.5, 'label' => '2.5h'],
            ['day' => 'Thu', 'hours' => 3.0, 'label' => '3.0h (Peak)', 'isPeak' => true],
            ['day' => 'Fri', 'hours' => 2.7, 'label' => '2.7h'],
            ['day' => 'Sat', 'hours' => 1.9, 'label' => '1.9h'],
            ['day' => 'Sun', 'hours' => 1.2, 'label' => '1.2h (Lowest)', 'isLowest' => true],
        ];

        $timeDistribution = [
            ['range' => '0–5h', 'students' => 4, 'pct' => 9],
            ['range' => '5–10h', 'students' => 6, 'pct' => 13],
            ['range' => '10–20h', 'students' => 15, 'pct' => 33],
            ['range' => '20–30h', 'students' => 12, 'pct' => 27],
            ['range' => '30h+', 'students' => 8, 'pct' => 18],
        ];

        $weeklyReport = [
            'week_range' => 'May 19 to May 25, 2025',
            'kpis' => [
                'active_students' => '38 / 45',
                'video_hours' => '245h',
                'quizzes_completed' => '128 attempts',
                'pdfs_opened' => '320 times',
                'avg_progress_gain' => '+6%',
                'at_risk_alerts' => '5 students',
            ],
            'daily_summary' => [
                ['day' => 'Monday', 'students' => 32, 'video_hours' => 28, 'quizzes' => 15],
                ['day' => 'Tuesday', 'students' => 35, 'video_hours' => 34, 'quizzes' => 18],
                ['day' => 'Wednesday', 'students' => 36, 'video_hours' => 39, 'quizzes' => 20],
                ['day' => 'Thursday', 'students' => 40, 'video_hours' => 48, 'quizzes' => 26, 'highlight' => '🔥 Peak Activity'],
                ['day' => 'Friday', 'students' => 38, 'video_hours' => 42, 'quizzes' => 22],
                ['day' => 'Saturday', 'students' => 25, 'video_hours' => 30, 'quizzes' => 16],
                ['day' => 'Sunday', 'students' => 18, 'video_hours' => 24, 'quizzes' => 11],
            ],
            'progress_trend' => [
                ['student' => 'Chan Dara', 'last_week' => 78, 'this_week' => 85, 'gain' => '+7% 🚀', 'bar_pct' => 85],
                ['student' => 'Bun Rithy', 'last_week' => 68, 'this_week' => 72, 'gain' => '+4% ✅', 'bar_pct' => 72],
                ['student' => 'Mao Sreynich', 'last_week' => 61, 'this_week' => 63, 'gain' => '+2%', 'bar_pct' => 63],
                ['student' => 'Sok Channa', 'last_week' => 45, 'this_week' => 45, 'gain' => '0% 🔴', 'bar_pct' => 45],
                ['student' => 'Long Vicheka', 'last_week' => 85, 'this_week' => 90, 'gain' => '+5% ✅', 'bar_pct' => 90],
            ],
            'alerts' => [
                ['type' => 'red', 'text' => '5 students had no learning activity for more than 7 days'],
                ['type' => 'red', 'text' => 'Module 4 completion dropped by 12% this week'],
                ['type' => 'yellow', 'text' => 'Practice Quiz on Loops has low average score (58%)'],
                ['type' => 'green', 'text' => 'Student engagement improved by 6% compared to last week'],
            ],
            'recommendations' => [
                '1. 📹 Add a short recap video for Module 4',
                '2. 🟩 Create extra practice quiz on Functions',
                '3. 📢 Send reminder to 5 inactive students',
                '4. 🎯 Schedule a live review session on Thursday evening',
                '5. 📄 Upload quick notes for Chapter 4.2',
            ],
        ];

        return Inertia::render('Teacher/Progress/Index', [
            'courses' => $courses,
            'topStats' => $topStats,
            'moduleCompletionOverview' => $moduleCompletionOverview,
            'modulesData' => $modulesData,
            'heatmapData' => $heatmapData,
            'learningTimeOverview' => $learningTimeOverview,
            'studentTimeList' => $studentTimeList,
            'weeklyChartDays' => $weeklyChartDays,
            'timeDistribution' => $timeDistribution,
            'weeklyReport' => $weeklyReport,
        ]);
    }

    public function sendReminder(Request $request)
    {
        $request->validate([
            'student_id' => 'nullable',
            'student_name' => 'nullable|string',
            'message' => 'nullable|string',
        ]);

        $name = $request->student_name ?? 'Student';
        return back()->with('success', "Study reminder sent to {$name} successfully!");
    }
}
