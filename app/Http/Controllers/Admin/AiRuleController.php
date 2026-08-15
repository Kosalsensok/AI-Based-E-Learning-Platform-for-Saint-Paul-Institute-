<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessAiRecommendation;
use App\Models\AiRecommendation;
use App\Services\AiRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class AiRuleController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab') ?: ($request->query('type') ?: 'rules');

        // Optimized eager loading with limit to avoid large memory allocations
        $recommendations = AiRecommendation::with(['user:id,name,email', 'lesson:id,title'])
            ->latest('created_at')
            ->take(100)
            ->get();

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

        // Automatically dispatch background evaluation job without blocking the request
        ProcessAiRecommendation::dispatch(null, ['source' => 'rule_update']);

        return back()->with('success', 'AI Recommendation Rules updated! Background evaluation started.');
    }

    public function evaluateRules(Request $request)
    {
        $userId = $request->input('user_id');
        
        // Dispatch to background queue worker
        ProcessAiRecommendation::dispatch($userId ? (int)$userId : null, [
            'triggered_by' => auth()->id() ?? 1,
            'source' => 'manual_trigger',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'AI Rule evaluation dispatched to background queue worker!',
        ]);
    }
}
