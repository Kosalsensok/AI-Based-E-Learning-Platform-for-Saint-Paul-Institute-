<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PracticeLabController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'it');
        return Inertia::render('Student/PracticeLab/Index', [
            'activeTab' => $tab,
        ]);
    }

    public function it(Request $request)
    {
        return Inertia::render('Student/PracticeLab/ItLab');
    }

    public function tourism(Request $request)
    {
        return Inertia::render('Student/PracticeLab/TourismLab');
    }

    public function english(Request $request)
    {
        return Inertia::render('Student/PracticeLab/EnglishLab');
    }

    public function agronomy(Request $request)
    {
        return Inertia::render('Student/PracticeLab/AgronomyLab');
    }

    public function socialWork(Request $request)
    {
        return Inertia::render('Student/PracticeLab/SocialWorkLab');
    }
}
