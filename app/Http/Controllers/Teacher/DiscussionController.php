<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\StudentQuestion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiscussionController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = $request->user()->id;

        $courses = Course::where('teacher_id', $teacherId)->get();
        if ($courses->isEmpty()) {
            $courses = collect([
                (object)['id' => 1, 'title' => 'C Programming Basics', 'code' => 'CS101'],
                (object)['id' => 2, 'title' => 'Computer Networks', 'code' => 'CS202'],
            ]);
        }

        // 1. Student Questions Data
        $questionsStats = [
            'unanswered' => 3,
            'answered' => 24,
            'pending' => 5,
            'urgent' => 1,
        ];

        $studentQuestions = [
            [
                'id' => 1,
                'code' => 'QST-2025-001',
                'question' => 'Why is my loop infinite? I wrote while(i<10) but it never stops.',
                'student_name' => 'Bun Rithy',
                'student_id_code' => 'STU24002',
                'student_major' => 'IT & Networking',
                'learning_mode' => 'Instructor-Led',
                'avatar' => '👨‍💻',
                'course_name' => 'C Programming Basics',
                'module_name' => 'Module 2',
                'chapter_name' => 'Ch 2.3 · Loops',
                'status' => 'new',
                'priority' => 'urgent',
                'urgent' => true,
                'replies_count' => 2,
                'created_at' => '15m ago',
                'attachments' => ['Screenshot_Code.png'],
                'replies' => [
                    [
                        'author' => 'Bun Rithy',
                        'role' => 'student',
                        'avatar' => '👨‍💻',
                        'time' => '15m ago',
                        'text' => 'Why is my loop infinite? I wrote while(i<10) but it never stops.',
                        'attachment' => 'Screenshot_Code.png',
                    ]
                ]
            ],
            [
                'id' => 2,
                'code' => 'QST-2025-002',
                'question' => 'How do I install GCC on Mac? The video tutorial shows Windows environment.',
                'student_name' => 'Sok Chanra',
                'student_id_code' => 'STU24003',
                'student_major' => 'Tourism Mgt',
                'learning_mode' => 'Self-Paced',
                'avatar' => '👩‍🎓',
                'course_name' => 'C Programming Basics',
                'module_name' => 'Module 1',
                'chapter_name' => 'Ch 1.2 · Setup Env',
                'status' => 'in_progress',
                'priority' => 'normal',
                'urgent' => false,
                'replies_count' => 1,
                'created_at' => '1h ago',
                'attachments' => [],
                'replies' => [
                    [
                        'author' => 'Sok Chanra',
                        'role' => 'student',
                        'avatar' => '👩‍🎓',
                        'time' => '1h ago',
                        'text' => 'How do I install GCC on Mac? The video shows Windows.',
                    ]
                ]
            ],
            [
                'id' => 3,
                'code' => 'QST-2025-003',
                'question' => 'Thank you for explaining compiler and linker concepts so clearly in Chapter 1.1!',
                'student_name' => 'Chan Dara',
                'student_id_code' => 'STU24001',
                'student_major' => 'IT & Networking',
                'learning_mode' => 'Instructor-Led',
                'avatar' => '👨‍🎓',
                'course_name' => 'C Programming Basics',
                'module_name' => 'Module 1',
                'chapter_name' => 'Ch 1.1 · History',
                'status' => 'resolved',
                'priority' => 'normal',
                'urgent' => false,
                'replies_count' => 3,
                'created_at' => '2h ago',
                'answer' => 'You are very welcome Dara! Keep practicing the compiling commands.',
                'attachments' => [],
                'replies' => [
                    [
                        'author' => 'Chan Dara',
                        'role' => 'student',
                        'avatar' => '👨‍🎓',
                        'time' => '2h ago',
                        'text' => 'Is a compiler different from an interpreter?',
                    ],
                    [
                        'author' => 'Mr. Sophea (Teacher)',
                        'role' => 'teacher',
                        'avatar' => '👨‍🏫',
                        'time' => '1h 45m ago',
                        'text' => 'Yes! A compiler translates the entire source code into binary before execution, while an interpreter translates line-by-line.',
                    ],
                    [
                        'author' => 'Chan Dara',
                        'role' => 'student',
                        'avatar' => '👨‍🎓',
                        'time' => '1h 30m ago',
                        'text' => 'Thank you for explaining compiler concept clearly! 🙏',
                    ],
                ]
            ],
        ];

        // 2. FAQs Data
        $faqs = [
            [
                'id' => 1,
                'question' => 'What is a compiler in C programming?',
                'answer' => 'A compiler is a special program that translates high-level C source code into executable machine code for CPU execution.',
                'course' => 'C Programming Basics',
                'module' => 'Module 1',
                'added_by' => 'Mr. Sophea',
                'views' => 142,
            ],
            [
                'id' => 2,
                'question' => 'Why does my loop execute infinitely?',
                'answer' => 'An infinite loop occurs when the loop termination condition (e.g. counter variable update like i++) is missing or never evaluates to false inside the loop body.',
                'course' => 'C Programming Basics',
                'module' => 'Module 2',
                'added_by' => 'Mr. Sophea',
                'views' => 98,
            ],
        ];

        // 3. Announcements Data
        $announcementsList = [
            [
                'id' => 1,
                'title_kh' => 'Module 3 បានបើកហើយ — សូមចាប់ផ្តើមរៀន',
                'title_en' => 'New Module 3: Control Flow is Ready',
                'category' => 'Course Update',
                'audience' => 'IT & Networking (520 Students)',
                'priority' => 'high',
                'status' => 'published',
                'created_at' => '16 Jun 2025',
                'pinned' => true,
                'content_kh' => 'សូមជម្រាបជូនសិស្ស IT & Networking ថា Module 3: Control Flow បានបើកហើយ។ សូមប្រើពេលវេលារៀនមេរៀននេះឱ្យបានល្អ។',
                'content_en' => 'Dear IT & Networking students, Module 3 is now open. Please complete previous modules before starting this one.',
                'action_text_kh' => 'ចាប់ផ្តើមរៀន',
                'action_text_en' => 'Start Learning',
                'action_url' => '/student/courses/c-programming/module-3',
                'analytics' => [
                    'views' => 320,
                    'total_students' => 520,
                    'views_pct' => 62,
                    'clicks' => 120,
                    'email_open' => 58,
                    'push_open' => 64,
                    'in_app' => 82,
                    'teacher_led_views' => 98,
                    'teacher_led_clicks' => 72,
                    'self_paced_views' => 85,
                    'self_paced_clicks' => 45,
                    'ai_insight' => 'Students who clicked "Start Learning" within 2 hours completed Module 3 30% faster than those who delayed.',
                ]
            ],
            [
                'id' => 2,
                'title_kh' => 'ការរំលឹកកាលបរិច្ឆេទបង់ប្រាក់សិក្សា (ABA Pay)',
                'title_en' => 'Payment Deadline Reminder for Pending Enrollees',
                'category' => 'Payment',
                'audience' => 'IT & Net (45 Pending)',
                'priority' => 'high',
                'status' => 'scheduled',
                'created_at' => '20 Jun 2025',
                'pinned' => false,
                'content_kh' => 'សូមជម្រាបជូនសិស្សដែលមានស្ថានភាព Pending ឱ្យបំពេញការបង់ប្រាក់ត្រឹមថ្ងៃទី ២០ ខែមិថុនា។',
                'content_en' => 'Please settle tuition fee payment via ABA Pay before 20 June to retain course access.',
                'action_text_kh' => 'បង់ប្រាក់ឥឡូវនេះ',
                'action_text_en' => 'Pay Now',
                'action_url' => '/student/payments',
                'analytics' => [
                    'views' => 45,
                    'total_students' => 45,
                    'views_pct' => 100,
                    'clicks' => 38,
                    'email_open' => 90,
                    'push_open' => 85,
                    'in_app' => 100,
                    'teacher_led_views' => 100,
                    'teacher_led_clicks' => 88,
                    'self_paced_views' => 100,
                    'self_paced_clicks' => 80,
                    'ai_insight' => 'Immediate push notifications increased payment verification rate by 40%.',
                ]
            ],
            [
                'id' => 3,
                'title_kh' => 'Quiz Post-Test Module 1 បានបើកហើយ',
                'title_en' => 'Quiz Post-Test Module 1 is Now Open',
                'category' => 'Quiz',
                'audience' => 'IT & Net (All)',
                'priority' => 'normal',
                'status' => 'published',
                'created_at' => '10 Jun 2025',
                'pinned' => false,
                'content_kh' => 'សូមចូលធ្វើ Quiz Post-Test ដើម្បើប្រឡងវាយតម្លៃចំណេះដឹង Module 1។',
                'content_en' => 'Post-Test for Module 1 is open. You have 2 attempts.',
                'action_text_kh' => 'ប្រឡង Quiz',
                'action_text_en' => 'Take Quiz',
                'action_url' => '/student/quiz/1',
                'analytics' => [
                    'views' => 480,
                    'total_students' => 520,
                    'views_pct' => 92,
                    'clicks' => 410,
                    'email_open' => 75,
                    'push_open' => 80,
                    'in_app' => 95,
                    'teacher_led_views' => 95,
                    'teacher_led_clicks' => 90,
                    'self_paced_views' => 88,
                    'self_paced_clicks' => 80,
                    'ai_insight' => 'Post-test participation peaked within 24h of announcement.',
                ]
            ],
        ];

        return Inertia::render('Teacher/Discussions/Index', [
            'courses' => $courses,
            'questionsStats' => $questionsStats,
            'questions' => $studentQuestions,
            'faqs' => $faqs,
            'announcements' => $announcementsList,
        ]);
    }

    public function answerQuestion(Request $request, $id)
    {
        $request->validate([
            'answer' => 'required|string',
            'mark_solved' => 'nullable|boolean',
            'add_faq' => 'nullable|boolean',
            'priority' => 'nullable|string',
        ]);

        return back()->with('success', 'Answer posted and student notified via Email & Push successfully!');
    }

    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'title_kh' => 'nullable|string',
            'title_en' => 'nullable|string',
            'content_kh' => 'nullable|string',
            'content_en' => 'nullable|string',
            'category' => 'nullable|string',
            'priority' => 'nullable|string',
        ]);

        return back()->with('success', 'Announcement published & broadcasted to students successfully!');
    }

    public function storeFaq(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        return back()->with('success', 'New FAQ added to course knowledge base!');
    }
}
