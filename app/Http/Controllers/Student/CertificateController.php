<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'my-certificates');
        return Inertia::render('Student/Certificates/Index', [
            'activeTab' => $tab,
            'certificates' => Certificate::where('student_id', $request->user()->id)->with('course')->get()
        ]);
    }

    public function myCertificates(Request $request)
    {
        return Inertia::render('Student/Certificates/MyCertificates');
    }

    public function downloadShare(Request $request)
    {
        return Inertia::render('Student/Certificates/DownloadShare');
    }

    public function verify(Request $request)
    {
        return Inertia::render('Student/Certificates/VerifyCertificate');
    }

    public function publicVerify(Request $request, $uuid = null)
    {
        $certificate = null;
        if ($uuid) {
            $certificate = Certificate::where('certificate_uuid', $uuid)
                ->with(['student', 'course'])
                ->first();
        }

        return Inertia::render('Student/Certificates/VerifyCertificate', [
            'publicCertUuid' => $uuid,
            'certificateData' => $certificate,
        ]);
    }
}
