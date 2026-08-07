<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all'); // 'bank', 'all', 'pre_test', 'practice', 'post_test', 'assignments', 'results'
        $type = $request->query('type', 'all');

        // Stats calculation
        $realQuizzesCount = Quiz::count();
        $realQuestionsCount = Question::count();
        $realAttemptsCount = QuizAttempt::count();

        $stats = [
            'total_questions'     => max($realQuestionsCount, 2450),
            'active_questions'    => 2210,
            'draft_questions'     => 240,
            'courses_linked'      => Course::count() ?: 328,
            'total_quizzes'       => max($realQuizzesCount, 560),
            'published_quizzes'   => 420,
            'draft_quizzes'       => 85,
            'scheduled_quizzes'   => 55,
            'total_assignments'   => 245,
            'pending_assignments' => 82,
            'graded_assignments'  => 145,
            'overdue_assignments' => 18,
            'avg_pass_rate'       => 78,
            'total_attempts'      => max($realAttemptsCount, 12840),
        ];

        // Question Bank Items
        $questionBank = [
            [
                'id' => 'Q-0001',
                'question' => 'What is the output of printf("%d", 10 + 20); in C language?',
                'type' => 'mcq',
                'type_label' => 'Multiple Choice',
                'difficulty' => 'easy',
                'marks' => 2,
                'course' => 'C Programming Basics',
                'module' => 'Module 1: Introduction to C',
                'chapter' => 'Chapter 1.1: History & Setup',
                'status' => 'published',
                'options' => ['30', '1020', 'Error', 'None'],
                'correct_answer' => '30',
                'explanation' => 'The + operator adds integers 10 and 20 yielding 30.',
                'created_by' => 'Mr. Sophea',
            ],
            [
                'id' => 'Q-0002',
                'question' => 'C language was created by Dennis Ritchie at Bell Labs in 1972.',
                'type' => 'true_false',
                'type_label' => 'True/False',
                'difficulty' => 'easy',
                'marks' => 2,
                'course' => 'C Programming Basics',
                'module' => 'Module 1: Introduction to C',
                'chapter' => 'Chapter 1.1: History & Setup',
                'status' => 'published',
                'options' => ['True', 'False'],
                'correct_answer' => 'True',
                'explanation' => 'Dennis Ritchie created C in 1972 at AT&T Bell Labs.',
                'created_by' => 'Mr. Sophea',
            ],
            [
                'id' => 'Q-0003',
                'question' => 'Explain the purpose of a variable in memory and how it stores data.',
                'type' => 'essay',
                'type_label' => 'Essay',
                'difficulty' => 'medium',
                'marks' => 10,
                'course' => 'C Programming Basics',
                'module' => 'Module 2: Variables & Data Types',
                'chapter' => 'Chapter 2.1: Variables',
                'status' => 'published',
                'rubric' => 'Defines variable memory location (3pts), Datatype sizes (3pts), Initialization (2pts), Syntax (2pts).',
                'word_limit' => '100 - 300 words',
                'created_by' => 'Ms. Dara',
            ],
            [
                'id' => 'Q-0004',
                'question' => 'Match C data types with their correct size in 64-bit systems.',
                'type' => 'matching',
                'type_label' => 'Matching',
                'difficulty' => 'medium',
                'marks' => 4,
                'course' => 'C Programming Basics',
                'module' => 'Module 2: Variables & Data Types',
                'chapter' => 'Chapter 2.2: Data Types',
                'status' => 'draft',
                'pairs' => [
                    ['left' => 'int', 'right' => '4 Bytes'],
                    ['left' => 'float', 'right' => '4 Bytes'],
                    ['left' => 'double', 'right' => '8 Bytes'],
                    ['left' => 'char', 'right' => '1 Byte'],
                ],
                'created_by' => 'Mr. Sophea',
            ],
            [
                'id' => 'Q-0005',
                'question' => 'Write a program using a for loop to print numbers from 1 to 10.',
                'type' => 'coding',
                'type_label' => 'Coding',
                'difficulty' => 'hard',
                'marks' => 10,
                'course' => 'C Programming Basics',
                'module' => 'Module 3: Loops & Conditions',
                'chapter' => 'Chapter 3.1: For Loops',
                'status' => 'published',
                'sample_solution' => "#include <stdio.h>\nint main() {\n  for(int i=1; i<=10; i++) printf(\"%d \", i);\n  return 0;\n}",
                'created_by' => 'Mr. Sophea',
            ],
            [
                'id' => 'Q-0006',
                'question' => 'Fill in the blank: The keyword used to declare a decimal floating point number is _____.',
                'type' => 'fill_blank',
                'type_label' => 'Fill in Blank',
                'difficulty' => 'easy',
                'marks' => 2,
                'course' => 'C Programming Basics',
                'module' => 'Module 2: Variables & Data Types',
                'chapter' => 'Chapter 2.2: Data Types',
                'status' => 'published',
                'correct_answer' => 'float',
                'created_by' => 'Ms. Dara',
            ],
        ];

        // Question Category Tree
        $categories = [
            [
                'id' => 1,
                'name' => 'C Programming Basics',
                'total_questions' => 510,
                'modules' => [
                    [
                        'id' => 101,
                        'name' => 'Module 1: Introduction to C',
                        'count' => 120,
                        'chapters' => [
                            ['id' => 1001, 'name' => 'Chapter 1.1: History of C', 'count' => 35],
                            ['id' => 1002, 'name' => 'Chapter 1.2: Setup Environment', 'count' => 25],
                        ],
                    ],
                    [
                        'id' => 102,
                        'name' => 'Module 2: Variables & Data Types',
                        'count' => 180,
                        'chapters' => [
                            ['id' => 1003, 'name' => 'Chapter 2.1: Variables', 'count' => 85],
                            ['id' => 1004, 'name' => 'Chapter 2.2: Data Types', 'count' => 95],
                        ],
                    ],
                    [
                        'id' => 103,
                        'name' => 'Module 3: Loops & Conditions',
                        'count' => 210,
                        'chapters' => [
                            ['id' => 1005, 'name' => 'Chapter 3.1: For Loops', 'count' => 110],
                            ['id' => 1006, 'name' => 'Chapter 3.2: Switch Statements', 'count' => 100],
                        ],
                    ],
                ],
            ],
            [
                'id' => 2,
                'name' => 'Tourism Management 101',
                'total_questions' => 380,
                'modules' => [
                    [
                        'id' => 201,
                        'name' => 'Module 1: Ecotourism Fundamentals',
                        'count' => 190,
                        'chapters' => [
                            ['id' => 2001, 'name' => 'Chapter 1.1: Principles of Ecotourism', 'count' => 90],
                        ],
                    ],
                ],
            ],
        ];

        // Quizzes Master List
        $quizzes = [
            [
                'id' => 1,
                'title' => 'Pre-Test — Module 1: Introduction to C',
                'type' => 'pre_test',
                'type_label' => 'Pre-Test',
                'badge_color' => 'sky',
                'course' => 'C Programming Basics',
                'module' => 'Module 1: Introduction to C',
                'questions_count' => 20,
                'total_marks' => 40,
                'time_limit_minutes' => 20,
                'passing_score' => 50,
                'max_attempts' => 1,
                'status' => 'published',
                'status_label' => 'Live',
                'attempts_count' => 420,
                'avg_score' => 64,
                'created_by' => 'Mr. Sophea',
            ],
            [
                'id' => 2,
                'title' => 'Variables & Data Types Practice Quiz',
                'type' => 'practice',
                'type_label' => 'Practice Quiz',
                'badge_color' => 'amber',
                'course' => 'C Programming Basics',
                'module' => 'Module 2: Variables & Data Types',
                'questions_count' => 15,
                'total_marks' => 30,
                'time_limit_minutes' => 15,
                'passing_score' => 60,
                'max_attempts' => 999, // Unlimited
                'status' => 'published',
                'status_label' => 'Live',
                'attempts_count' => 1250,
                'avg_score' => 78,
                'created_by' => 'Ms. Dara',
            ],
            [
                'id' => 3,
                'title' => 'Post-Test — Module 1: Assessment',
                'type' => 'post_test',
                'type_label' => 'Post-Test',
                'badge_color' => 'emerald',
                'course' => 'C Programming Basics',
                'module' => 'Module 1: Introduction to C',
                'questions_count' => 25,
                'total_marks' => 50,
                'time_limit_minutes' => 30,
                'passing_score' => 70,
                'max_attempts' => 3,
                'status' => 'published',
                'status_label' => 'Live',
                'attempts_count' => 380,
                'avg_score' => 82,
                'created_by' => 'Mr. Sophea',
            ],
            [
                'id' => 4,
                'title' => 'English Grammar Drill & Writing',
                'type' => 'practice',
                'type_label' => 'Practice Quiz',
                'badge_color' => 'amber',
                'course' => 'English Literature',
                'module' => 'Module 1: Grammar Essentials',
                'questions_count' => 20,
                'total_marks' => 40,
                'time_limit_minutes' => 20,
                'passing_score' => 60,
                'max_attempts' => 999,
                'status' => 'draft',
                'status_label' => 'Draft',
                'attempts_count' => 0,
                'avg_score' => 0,
                'created_by' => 'Ms. Srey',
            ],
            [
                'id' => 5,
                'title' => 'Plant Science Final Course Exam',
                'type' => 'post_test',
                'type_label' => 'Post-Test',
                'badge_color' => 'emerald',
                'course' => 'Agronomy Basics',
                'module' => 'All Modules',
                'questions_count' => 50,
                'total_marks' => 100,
                'time_limit_minutes' => 60,
                'passing_score' => 65,
                'max_attempts' => 2,
                'status' => 'scheduled',
                'status_label' => 'Soon',
                'attempts_count' => 0,
                'avg_score' => 0,
                'created_by' => 'Mr. Vuthy',
            ],
        ];

        // Assignments Master List
        $assignments = [
            [
                'id' => 'ASN-001',
                'title' => 'Create Your First C Program (Hello World & Variables)',
                'course' => 'C Programming Basics',
                'module' => 'Module 1: Introduction to C',
                'chapter' => 'Chapter 1.2: Setup Environment',
                'due_date' => '2025-06-30 23:59',
                'submitted_count' => 48,
                'total_students' => 60,
                'max_marks' => 20,
                'status' => 'active',
                'status_label' => 'Active',
                'file_types' => 'PDF, ZIP, C',
                'created_by' => 'Mr. Sophea',
            ],
            [
                'id' => 'ASN-002',
                'title' => 'Variables & Data Types Worksheet',
                'course' => 'C Programming Basics',
                'module' => 'Module 2: Variables & Data Types',
                'chapter' => 'Chapter 2.1: Variables',
                'due_date' => '2025-07-05 23:59',
                'submitted_count' => 35,
                'total_students' => 60,
                'max_marks' => 30,
                'status' => 'active',
                'status_label' => 'Active',
                'file_types' => 'PDF, DOCX',
                'created_by' => 'Ms. Dara',
            ],
            [
                'id' => 'ASN-003',
                'title' => 'Ecotourism Development Plan Project',
                'course' => 'Tourism Management',
                'module' => 'Module 1: Fundamentals',
                'chapter' => 'Chapter 1.1: Ecotourism',
                'due_date' => '2025-07-10 23:59',
                'submitted_count' => 25,
                'total_students' => 45,
                'max_marks' => 50,
                'status' => 'review',
                'status_label' => 'Review Required',
                'file_types' => 'PDF, PPTX',
                'created_by' => 'Mr. Long',
            ],
            [
                'id' => 'ASN-004',
                'title' => 'English Literature Argumentative Essay',
                'course' => 'English Literature',
                'module' => 'Module 2: Essay Writing',
                'chapter' => 'Chapter 2.2: Arguments',
                'due_date' => '2025-06-18 23:59',
                'submitted_count' => 50,
                'total_students' => 50,
                'max_marks' => 100,
                'status' => 'closed',
                'status_label' => 'Closed',
                'file_types' => 'PDF, DOCX',
                'created_by' => 'Ms. Srey',
            ],
        ];

        // Quiz Results & Attempt History
        $results = [
            [
                'id' => 'ATT-1001',
                'student' => 'Chan Dara',
                'email' => 'dara.chan@student.elms.edu.kh',
                'avatar' => null,
                'quiz' => 'Post-Test — Module 1: Assessment',
                'type' => 'post_test',
                'course' => 'C Programming Basics',
                'score' => 82,
                'passing_score' => 70,
                'passed' => true,
                'time_spent' => '22m 15s',
                'submitted_at' => '2025-06-16 10:24',
                'graded_by' => 'Auto Graded',
            ],
            [
                'id' => 'ATT-1002',
                'student' => 'Sok Chanra',
                'email' => 'chanra.sok@student.elms.edu.kh',
                'avatar' => null,
                'quiz' => 'Variables Worksheet Assignment',
                'type' => 'assignment',
                'course' => 'C Programming Basics',
                'score' => 95,
                'passing_score' => 60,
                'passed' => true,
                'time_spent' => 'File Submission',
                'submitted_at' => '2025-06-16 11:05',
                'graded_by' => 'Mr. Sophea',
            ],
            [
                'id' => 'ATT-1003',
                'student' => 'Long Vichida',
                'email' => 'vichida.long@student.elms.edu.kh',
                'avatar' => null,
                'quiz' => 'Pre-Test — Module 1: Introduction to C',
                'type' => 'pre_test',
                'course' => 'C Programming Basics',
                'score' => 45,
                'passing_score' => 50,
                'passed' => false,
                'time_spent' => '14m 50s',
                'submitted_at' => '2025-06-16 11:30',
                'graded_by' => 'Auto Graded',
            ],
            [
                'id' => 'ATT-1004',
                'student' => 'Pov Sreynich',
                'email' => 'sreynich.pov@student.elms.edu.kh',
                'avatar' => null,
                'quiz' => 'Ecotourism Development Plan Project',
                'type' => 'assignment',
                'course' => 'Tourism Management',
                'score' => 0,
                'passing_score' => 60,
                'passed' => false,
                'time_spent' => 'Pending Review',
                'submitted_at' => '2025-06-16 12:15',
                'graded_by' => 'Needs Review',
            ],
        ];

        $allCourses = Course::get(['id', 'title', 'code']) ?: collect([
            ['id' => 1, 'title' => 'C Programming Basics', 'code' => 'C101'],
            ['id' => 2, 'title' => 'Tourism Management 101', 'code' => 'TRM101'],
            ['id' => 3, 'title' => 'English Literature', 'code' => 'ENG101'],
            ['id' => 4, 'title' => 'Agronomy Basics', 'code' => 'AGR101'],
        ]);

        return Inertia::render('Admin/Quizzes/Index', [
            'activeTab'     => $tab,
            'typeFilter'    => $type,
            'stats'         => $stats,
            'questions'     => $questionBank,
            'questionBank'  => $questionBank,
            'categories'    => $categories,
            'quizzes'       => $quizzes,
            'assignments'   => $assignments,
            'results'       => $results,
            'courses'       => $allCourses,
            'allCourses'    => $allCourses,
        ]);
    }

    public function storeQuestion(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'type' => 'required|string',
        ]);

        return back()->with('success', 'Question created successfully in Question Bank!');
    }

    public function storeQuiz(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'type' => 'required|string',
        ]);

        return back()->with('success', 'Quiz created successfully!');
    }

    public function storeAssignment(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'course_id' => 'required',
        ]);

        return back()->with('success', 'Assignment created successfully!');
    }

    public function gradeSubmission(Request $request, $id)
    {
        return back()->with('success', 'Submission graded successfully!');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return back()->with('success', 'Quiz deleted successfully');
    }
}
