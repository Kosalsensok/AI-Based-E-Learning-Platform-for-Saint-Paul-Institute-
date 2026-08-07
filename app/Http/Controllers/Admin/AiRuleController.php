<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiRecommendation;
use App\Services\AiRecommendationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AiRuleController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab') ?: ($request->query('type') ?: 'rules');

        $recommendations = AiRecommendation::with(['user', 'lesson'])->latest()->get();

        return Inertia::render('Admin/AiRecommendationModule/Index', [
            'activeTab' => $tab,
            'recommendations' => $recommendations,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'advance_threshold'  => 'required|integer|min:0|max:100',
            'review_threshold'   => 'required|integer|min:0|max:100',
            'remedial_threshold' => 'required|integer|min:0|max:100',
            'idle_days'          => 'required|integer|min:1|max:30',
        ]);

        app(AiRecommendationService::class)->updateRules([
            'advance_next'   => ['min_score' => $data['advance_threshold']],
            'review_current' => ['min_score' => $data['review_threshold']],
            'remedial'       => ['max_score' => $data['remedial_threshold']],
            're_engage'      => ['max_idle_days' => $data['idle_days']],
        ]);

        return back()->with('success', 'AI Recommendation Rules updated successfully!');
    }
}
