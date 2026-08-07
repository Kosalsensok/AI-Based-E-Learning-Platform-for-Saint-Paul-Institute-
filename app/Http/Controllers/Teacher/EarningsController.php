<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EarningsController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = $request->user()->id;

        $courses = Course::where('teacher_id', $teacherId)
            ->where('is_paid', true)
            ->get();

        $revenueList = $courses->map(function($course) {
            $paidStudentsCount = rand(10, 50);
            $grossRevenue = $paidStudentsCount * ($course->price ?: 45);
            $teacherShare = $grossRevenue * 0.70;

            return [
                'id' => $course->id,
                'title' => $course->title,
                'price' => $course->price ?: 45,
                'paid_students' => $paidStudentsCount,
                'gross_revenue' => $grossRevenue,
                'teacher_share' => $teacherShare,
            ];
        });

        $totalRevenue = $revenueList->sum('teacher_share');

        $payoutHistory = [
            [
                'id' => 'PO-1029',
                'amount' => 1250.00,
                'method' => 'ABA Bank Transfer',
                'status' => 'completed',
                'requested_at' => now()->subWeeks(2)->format('Y-m-d'),
            ],
            [
                'id' => 'PO-1088',
                'amount' => 980.00,
                'method' => 'ABA Bank Transfer',
                'status' => 'completed',
                'requested_at' => now()->subMonth()->format('Y-m-d'),
            ]
        ];

        return Inertia::render('Teacher/Earnings/Index', [
            'revenueList' => $revenueList,
            'totalRevenue' => $totalRevenue,
            'payoutHistory' => $payoutHistory,
        ]);
    }

    public function requestPayout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
        ]);

        return back()->with('success', 'Payout request submitted to administrator successfully!');
    }
}
