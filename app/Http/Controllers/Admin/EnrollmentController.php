<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EnrollmentController extends Controller
{
    public function majorEnrollments(): Response
    {
        return Inertia::render('Admin/EnrollmentModule/MajorEnrollments', [
            'summaryStats' => [
                'totalStudents' => 2458,
                'totalCourseEnrolls' => 4250,
                'paidEnrolls' => 3120,
                'pendingEnrolls' => 480,
                'freeEnrolls' => 650,
            ],
        ]);
    }

    public function courseEnrollments(): Response
    {
        return Inertia::render('Admin/EnrollmentModule/CourseEnrollments', [
            'summaryStats' => [
                'totalStudents' => 2458,
                'totalCourseEnrolls' => 4250,
                'paidEnrolls' => 3120,
                'pendingEnrolls' => 480,
                'freeEnrolls' => 650,
            ],
        ]);
    }

    public function singleEnrollment(): Response
    {
        return Inertia::render('Admin/EnrollmentModule/SingleEnrollment');
    }

    public function bulkEnrollment(): Response
    {
        return Inertia::render('Admin/EnrollmentModule/BulkEnrollment');
    }

    public function enrollmentHistory(): Response
    {
        return Inertia::render('Admin/EnrollmentModule/EnrollmentHistory', [
            'summaryStats' => [
                'totalStudents' => 2458,
                'totalCourseEnrolls' => 4250,
                'paidEnrolls' => 3120,
                'pendingEnrolls' => 480,
                'freeEnrolls' => 650,
            ],
        ]);
    }

    public function storeMajorEnrollment(Request $request)
    {
        return redirect()->back()->with('success', 'Student enrolled to major successfully.');
    }

    public function transferMajor(Request $request, $id)
    {
        return redirect()->back()->with('success', 'Student transferred major successfully.');
    }

    public function withdrawMajor($id)
    {
        return redirect()->back()->with('success', 'Student withdrawn from major.');
    }

    public function storeCourseEnrollment(Request $request)
    {
        return redirect()->back()->with('success', 'Course enrollment saved.');
    }

    public function toggleAccess(Request $request, $id)
    {
        return redirect()->back()->with('success', 'Course lesson access updated.');
    }

    public function verifyPayment(Request $request, $id)
    {
        return redirect()->back()->with('success', 'Payment verified and access unlocked.');
    }

    public function removeCourseEnrollment($id)
    {
        return redirect()->back()->with('success', 'Course enrollment removed.');
    }

    public function storeSingleEnrollment(Request $request)
    {
        return redirect()->back()->with('success', 'Single enrollment wizard completed.');
    }

    public function reverseHistoryAction(Request $request, $id)
    {
        return redirect()->back()->with('success', 'Audit log action reversed.');
    }
}
