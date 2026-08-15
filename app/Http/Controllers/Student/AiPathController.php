<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AiPathController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommended');
        return Inertia::render('Student/AiLearningPath/Index', [
            'activeTab' => $tab,
        ]);
    }

    public function recommended(Request $request)
    {
        return Inertia::render('Student/AiLearningPath/RecommendedLesson');
    }

    public function review(Request $request)
    {
        return Inertia::render('Student/AiLearningPath/ReviewLesson');
    }

    public function weakTopics(Request $request)
    {
        return Inertia::render('Student/AiLearningPath/WeakTopics');
    }

    public function nextModule(Request $request)
    {
        return Inertia::render('Student/AiLearningPath/NextModule');
    }

    public function nextCourse(Request $request)
    {
        return Inertia::render('Student/AiLearningPath/NextCourse');
    }
}
