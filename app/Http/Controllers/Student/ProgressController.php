<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'overview');
        return Inertia::render('Student/ProgressTracking/Index', [
            'activeTab' => $tab,
            'progress' => LessonProgress::where('user_id', $request->user()->id)->with('lesson.course')->get()
        ]);
    }

    public function overview(Request $request)
    {
        return Inertia::render('Student/ProgressTracking/Overview');
    }

    public function learningTime(Request $request)
    {
        return Inertia::render('Student/ProgressTracking/LearningTime');
    }

    public function weeklyProgress(Request $request)
    {
        return Inertia::render('Student/ProgressTracking/WeeklyProgress');
    }

    public function achievementsBadges(Request $request)
    {
        return Inertia::render('Student/ProgressTracking/AchievementsBadges');
    }
}
