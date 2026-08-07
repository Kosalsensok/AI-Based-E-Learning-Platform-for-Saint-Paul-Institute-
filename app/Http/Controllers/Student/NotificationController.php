<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');
        return Inertia::render('Student/Notifications/Index', [
            'activeTab' => $tab,
        ]);
    }

    public function announcements(Request $request)
    {
        return Inertia::render('Student/Notifications/Announcements');
    }

    public function aiAlerts(Request $request)
    {
        return Inertia::render('Student/Notifications/AiAlerts');
    }

    public function teacherFeedback(Request $request)
    {
        return Inertia::render('Student/Notifications/TeacherFeedback');
    }

    public function paymentAlerts(Request $request)
    {
        return Inertia::render('Student/Notifications/PaymentAlerts');
    }
}
