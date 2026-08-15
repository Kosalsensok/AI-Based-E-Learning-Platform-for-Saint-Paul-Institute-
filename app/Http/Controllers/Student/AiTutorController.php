<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AiTutorController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'english');
        return Inertia::render('Student/AiTutor/Index', [
            'activeTab' => $tab,
        ]);
    }

    public function english(Request $request)
    {
        return Inertia::render('Student/AiTutor/EnglishTutor');
    }

    public function chat(Request $request)
    {
        return Inertia::render('Student/AiTutor/ChatSupport');
    }

    public function feedback(Request $request)
    {
        return Inertia::render('Student/AiTutor/InstantFeedback');
    }
}
