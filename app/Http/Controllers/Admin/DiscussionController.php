<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscussionThread;
use App\Models\DiscussionComment;
use App\Models\StudentQuestion;
use App\Models\SupportTicket;
use App\Models\ReportedContent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiscussionController extends Controller
{
    private function getTopStats()
    {
        return [
            'total_discussions' => 1245,
            'open_discussions' => 860,
            'closed_discussions' => 385,
            'total_questions' => 890,
            'unanswered_questions' => 47,
            'avg_reply_time' => '2h 15m',
            'support_tickets' => 134,
            'high_priority_tickets' => 12,
            'sla_met_pct' => '96.2%',
            'reported_content_pending' => 28,
        ];
    }

    /**
     * 1. 💬 Discussions View (Slack 3-Panel Layout)
     */
    public function discussions(Request $request)
    {
        $threads = DiscussionThread::latest()->get();
        if ($threads->isEmpty()) {
            $threads = collect([
                [
                    'id' => 1,
                    'title' => 'How to solve loop in C?',
                    'course_name' => 'C Programming',
                    'module_name' => 'Module 3',
                    'author_name' => 'Chan Dara',
                    'author_role' => 'student',
                    'replies_count' => 12,
                    'likes_count' => 18,
                    'views_count' => 245,
                    'status' => 'solved',
                    'is_pinned' => true,
                    'is_locked' => false,
                    'is_solved' => true,
                    'is_hot' => true,
                    'toxic_score' => 2,
                    'tags' => ['#C', '#Loops', '#Help'],
                    'last_activity' => '2h ago',
                    'comments' => [
                        [
                            'id' => 101,
                            'author_name' => 'Mr. Sophea',
                            'author_role' => 'teacher',
                            'content' => 'To prevent infinite loops, make sure your counter variable increments inside the loop body, e.g. for(int i=0; i<10; i++).',
                            'likes_count' => 15,
                            'is_best_answer' => true,
                            'is_teacher_answer' => true,
                            'toxic_score' => 0,
                            'created_at' => '1h 45m ago'
                        ],
                        [
                            'id' => 102,
                            'author_name' => 'Bun Rithy',
                            'author_role' => 'student',
                            'content' => 'Thanks Mr. Sophea! That fixed my segmentation fault issue.',
                            'likes_count' => 3,
                            'is_best_answer' => false,
                            'is_teacher_answer' => false,
                            'toxic_score' => 1,
                            'created_at' => '1h 10m ago'
                        ]
                    ]
                ],
                [
                    'id' => 2,
                    'title' => 'Array vs Pointer in memory allocation?',
                    'course_name' => 'Data Structure',
                    'module_name' => 'Module 2',
                    'author_name' => 'Bun Rithy',
                    'author_role' => 'student',
                    'replies_count' => 3,
                    'likes_count' => 5,
                    'views_count' => 110,
                    'status' => 'open',
                    'is_pinned' => false,
                    'is_locked' => false,
                    'is_solved' => false,
                    'is_hot' => false,
                    'toxic_score' => 1,
                    'tags' => ['#Pointers', '#Memory'],
                    'last_activity' => '5h ago',
                    'comments' => []
                ],
                [
                    'id' => 3,
                    'title' => 'Official Rules of Final Exam',
                    'course_name' => 'All Majors',
                    'module_name' => 'General',
                    'author_name' => 'Admin User',
                    'author_role' => 'admin',
                    'replies_count' => 45,
                    'likes_count' => 88,
                    'views_count' => 1250,
                    'status' => 'locked',
                    'is_pinned' => true,
                    'is_locked' => true,
                    'is_solved' => false,
                    'is_hot' => true,
                    'toxic_score' => 0,
                    'tags' => ['#Exam', '#Rules'],
                    'last_activity' => '1d ago',
                    'comments' => []
                ]
            ]);
        }

        return Inertia::render('Admin/DiscussionModule/Discussions', [
            'threads' => $threads,
            'topStats' => $this->getTopStats(),
        ]);
    }

    /**
     * 2. ❓ Student Questions (Q&A) View
     */
    public function questions(Request $request)
    {
        $questions = StudentQuestion::latest()->get();
        if ($questions->isEmpty()) {
            $questions = collect([
                [
                    'question_code' => 'Q-2025-032',
                    'question_text' => 'What is the exact difference between an array name and a pointer variable in C?',
                    'course_name' => 'C Programming',
                    'chapter_name' => 'Chapter 2.2',
                    'student_name' => 'Sok Channa',
                    'student_year' => 'Year 2',
                    'views_count' => 12,
                    'status' => 'pending',
                    'priority' => 'high',
                    'sla_overdue' => true, // > 6h
                    'ai_confidence' => 92,
                    'ai_draft_answer' => 'An array name is a constant pointer to the first element, whereas a pointer variable stores a memory address and can be reassigned to point elsewhere.',
                    'time_ago' => '5h ago',
                ],
                [
                    'question_code' => 'Q-2025-031',
                    'question_text' => 'Can you explain recursive function base cases again?',
                    'course_name' => 'Data Structures',
                    'chapter_name' => 'Chapter 4.1',
                    'student_name' => 'Bun Rithy',
                    'student_year' => 'Year 1',
                    'views_count' => 45,
                    'status' => 'answered',
                    'priority' => 'medium',
                    'sla_overdue' => false,
                    'answered_by' => 'Mr. Sophea',
                    'final_answer' => 'A base case is the condition in a recursive algorithm that stops the recursion from continuing infinitely.',
                    'helpful_votes' => 5,
                    'time_ago' => '1d ago',
                ]
            ]);
        }

        return Inertia::render('Admin/DiscussionModule/StudentQuestions', [
            'questions' => $questions,
            'topStats' => $this->getTopStats(),
        ]);
    }

    /**
     * 3. 🎫 Support Tickets View (Kanban Board + Table)
     */
    public function tickets(Request $request)
    {
        $tickets = SupportTicket::latest()->get();
        if ($tickets->isEmpty()) {
            $tickets = collect([
                [
                    'ticket_code' => 'TK-001',
                    'student_name' => 'Chan Dara',
                    'student_email' => 'dara@example.com',
                    'subject' => 'Can\'t login after ABA payment',
                    'category' => 'Login',
                    'priority' => 'high',
                    'assigned_to' => 'Admin User',
                    'sla_time_left' => '⏰ 2h left',
                    'status' => 'new',
                    'message' => 'I paid $45 via ABA Bank app yesterday but my account is still showing locked status.',
                    'evidence_url' => 'ABA_receipt.jpg',
                    'created_at' => '26/05 10:50 AM',
                    'timeline' => [
                        ['time' => '10:50 AM', 'event' => '🎫 Ticket Created (Web)'],
                        ['time' => '10:52 AM', 'event' => '🤖 System: Auto-linked to Transaction TXN-05781 ✅ Success'],
                        ['time' => '10:55 AM', 'event' => '👤 Admin User: Internal Note "Payment verified, need to unlock manually"'],
                    ]
                ],
                [
                    'ticket_code' => 'TK-002',
                    'student_name' => 'Bun Rithy',
                    'student_email' => 'rithy@example.com',
                    'subject' => 'ABA transaction failed twice',
                    'category' => 'Payment',
                    'priority' => 'high',
                    'assigned_to' => 'Finance Dept',
                    'sla_time_left' => '⏰ Overdue',
                    'status' => 'open',
                    'message' => 'Money was deducted from my account but order status failed.',
                    'created_at' => '25/05 14:00 PM',
                ],
                [
                    'ticket_code' => 'TK-003',
                    'student_name' => 'Sok Channa',
                    'student_email' => 'channa@example.com',
                    'subject' => 'Video not playing on iOS Safari',
                    'category' => 'Content',
                    'priority' => 'medium',
                    'assigned_to' => 'Content Admin',
                    'sla_time_left' => '18h left',
                    'status' => 'waiting',
                    'message' => 'The video stream buffers continuously on my iPad.',
                    'created_at' => '25/05 09:15 AM',
                ],
                [
                    'ticket_code' => 'TK-004',
                    'student_name' => 'Pov Sreynich',
                    'student_email' => 'sreynich@example.com',
                    'subject' => 'Certificate download error solved',
                    'category' => 'Account',
                    'priority' => 'low',
                    'assigned_to' => 'Support Desk',
                    'sla_time_left' => 'Resolved',
                    'status' => 'resolved',
                    'message' => 'Thank you for fixing my certificate PDF generation.',
                    'created_at' => '24/05 11:00 AM',
                ],
            ]);
        }

        return Inertia::render('Admin/DiscussionModule/SupportTickets', [
            'tickets' => $tickets,
            'topStats' => $this->getTopStats(),
        ]);
    }

    /**
     * 4. 🚩 Reported Content View (Moderation Queue)
     */
    public function reports(Request $request)
    {
        $reports = ReportedContent::latest()->get();
        if ($reports->isEmpty()) {
            $reports = collect([
                [
                    'report_code' => 'RPT-021',
                    'reported_by' => 'Bun Rithy (Student)',
                    'reason' => '💬 Harassment',
                    'content_type' => 'Comment',
                    'author_name' => 'UserX',
                    'comment_text' => 'You are stupid and your code makes zero sense...',
                    'toxic_score' => 85,
                    'status' => 'pending',
                    'time_ago' => '1h ago',
                    'context_link' => 'Discussion: How to solve loop?'
                ],
                [
                    'report_code' => 'RPT-020',
                    'reported_by' => 'Teacher Sophea',
                    'reason' => '📝 Cheating - Sharing Post-Test answers',
                    'content_type' => 'PDF Upload',
                    'author_name' => 'StudentY',
                    'comment_text' => 'PDF uploaded containing complete answer keys for Post-Test 2.',
                    'toxic_score' => 95,
                    'status' => 'pending',
                    'time_ago' => '3h ago',
                    'context_link' => 'Course: C Programming'
                ]
            ]);
        }

        return Inertia::render('Admin/DiscussionModule/ReportedContent', [
            'reports' => $reports,
            'topStats' => $this->getTopStats(),
        ]);
    }
}
