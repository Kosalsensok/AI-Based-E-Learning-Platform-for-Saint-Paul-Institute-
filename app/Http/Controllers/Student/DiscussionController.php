<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiscussionController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'ask');
        return Inertia::render('Student/Discussions/Index', [
            'activeTab' => $tab,
        ]);
    }

    public function ask(Request $request)
    {
        return Inertia::render('Student/Discussions/AskQuestion');
    }

    public function myQuestions(Request $request)
    {
        return Inertia::render('Student/Discussions/MyQuestions');
    }

    public function courseDiscussions(Request $request)
    {
        return Inertia::render('Student/Discussions/CourseDiscussions');
    }

    public function faq(Request $request)
    {
        return Inertia::render('Student/Discussions/CourseFaq');
    }

    public function tickets(Request $request)
    {
        return Inertia::render('Student/Discussions/SupportTicket');
    }
}
