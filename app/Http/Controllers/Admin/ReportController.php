<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'overview');

        return Inertia::render('Admin/AnalyticsModule/Index', [
            'activeTab' => $tab,
        ]);
    }

    public function exportFinancials()
    {
        $payments = \App\Models\Payment::with(['student', 'course'])->where('status', 'paid')->get();
        
        $csvData = "Payment ID,Student,Course,Amount,Transaction ID,Date\n";
        foreach ($payments as $payment) {
            $csvData .= "{$payment->id},\"{$payment->student?->name}\",\"{$payment->course?->title}\",{$payment->amount},{$payment->aba_transaction_id},{$payment->created_at}\n";
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="financial_report.csv"');
    }

    public function exportEnrollments()
    {
        $enrollments = \App\Models\Enrollment::with(['student', 'course'])->get();
        
        $csvData = "Enrollment ID,Student,Course,Status,Date\n";
        foreach ($enrollments as $enrollment) {
            $csvData .= "{$enrollment->id},\"{$enrollment->student?->name}\",\"{$enrollment->course?->title}\",{$enrollment->status},{$enrollment->created_at}\n";
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="enrollment_data.csv"');
    }
}
