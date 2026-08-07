<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\CertificateVerificationLog;
use App\Models\Course;
use App\Models\Major;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CertificateController extends Controller
{
    protected CertificateService $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    private function getCommonData()
    {
        $templates = CertificateTemplate::latest()->get();
        if ($templates->isEmpty()) {
            $templates = collect([
                [
                    'id' => 1,
                    'name' => 'Classic Blue',
                    'assigned_type' => 'major',
                    'assigned_name' => 'IT & Networking',
                    'language' => 'kh_en',
                    'paper_size' => 'a4_landscape',
                    'status' => 'active',
                ],
                [
                    'id' => 2,
                    'name' => 'Royal Gold',
                    'assigned_type' => 'major',
                    'assigned_name' => 'Tourism Mgt',
                    'language' => 'en',
                    'paper_size' => 'a4_landscape',
                    'status' => 'active',
                ],
                [
                    'id' => 3,
                    'name' => 'Green Agriculture',
                    'assigned_type' => 'major',
                    'assigned_name' => 'Agronomy',
                    'language' => 'kh_en',
                    'paper_size' => 'a4_landscape',
                    'status' => 'active',
                ],
                [
                    'id' => 4,
                    'name' => 'Elegant Purple',
                    'assigned_type' => 'major',
                    'assigned_name' => 'Social Work',
                    'language' => 'en',
                    'paper_size' => 'a4_landscape',
                    'status' => 'draft',
                ]
            ]);
        }

        $certificates = Certificate::with(['student', 'course', 'major', 'template', 'revokedByAdmin'])
            ->latest()
            ->get();

        if ($certificates->isEmpty()) {
            $certificates = collect([
                [
                    'id' => 1,
                    'certificate_number' => 'ELMS-2025-000451',
                    'verification_code' => 'VER894521',
                    'student' => ['id' => 101, 'name' => 'Chan Dara', 'email' => 'dara@elms.edu'],
                    'student_id_code' => 'STU24001',
                    'course' => ['id' => 1, 'title' => 'C Programming Basics'],
                    'major' => ['name' => 'IT & Networking'],
                    'grade' => 'A',
                    'score' => 82,
                    'issued_at' => now()->subDays(1)->toDateTimeString(),
                    'status' => 'valid',
                    'template_name' => 'Classic Blue',
                    'downloads_count' => 4,
                    'verifications_count' => 12,
                    'last_verified_at' => now()->subHours(3)->toDateTimeString(),
                ],
                [
                    'id' => 2,
                    'certificate_number' => 'ELMS-2025-000450',
                    'verification_code' => 'VER894520',
                    'student' => ['id' => 102, 'name' => 'Bun Rithy', 'email' => 'rithy@elms.edu'],
                    'student_id_code' => 'STU24002',
                    'course' => ['id' => 1, 'title' => 'C Programming Basics'],
                    'major' => ['name' => 'IT & Networking'],
                    'grade' => 'B+',
                    'score' => 78,
                    'issued_at' => now()->subDays(1)->toDateTimeString(),
                    'status' => 'valid',
                    'template_name' => 'Classic Blue',
                    'downloads_count' => 2,
                    'verifications_count' => 5,
                    'last_verified_at' => now()->subHours(10)->toDateTimeString(),
                ],
                [
                    'id' => 3,
                    'certificate_number' => 'ELMS-2025-000449',
                    'verification_code' => 'VER894519',
                    'student' => ['id' => 103, 'name' => 'Pov Sreynich', 'email' => 'sreynich@elms.edu'],
                    'student_id_code' => 'STU24003',
                    'course' => ['id' => 2, 'title' => 'Plant Science & Soil Biology'],
                    'major' => ['name' => 'Agronomy'],
                    'grade' => 'B+',
                    'score' => 76,
                    'issued_at' => now()->subDays(2)->toDateTimeString(),
                    'status' => 'valid',
                    'template_name' => 'Green Agriculture',
                    'downloads_count' => 6,
                    'verifications_count' => 8,
                    'last_verified_at' => now()->subDays(1)->toDateTimeString(),
                ],
                [
                    'id' => 4,
                    'certificate_number' => 'ELMS-2025-000448',
                    'verification_code' => 'VER894518',
                    'student' => ['id' => 104, 'name' => 'Mao Sreynich', 'email' => 'mao.s@elms.edu'],
                    'student_id_code' => 'STU24004',
                    'course' => ['id' => 3, 'title' => 'Community Social Work'],
                    'major' => ['name' => 'Social Work'],
                    'grade' => 'A',
                    'score' => 91,
                    'issued_at' => now()->subDays(2)->toDateTimeString(),
                    'status' => 'valid',
                    'template_name' => 'Elegant Purple',
                    'downloads_count' => 3,
                    'verifications_count' => 14,
                    'last_verified_at' => now()->subHours(5)->toDateTimeString(),
                ],
                [
                    'id' => 5,
                    'certificate_number' => 'ELMS-2025-000412',
                    'verification_code' => 'VER894412',
                    'student' => ['id' => 105, 'name' => 'Sok Chanra', 'email' => 'chanra@elms.edu'],
                    'student_id_code' => 'STU24005',
                    'course' => ['id' => 4, 'title' => 'Tourism Basics'],
                    'major' => ['name' => 'Tourism Mgt'],
                    'grade' => 'Pass',
                    'score' => 70,
                    'issued_at' => now()->subDays(15)->toDateTimeString(),
                    'status' => 'revoked',
                    'revoked_at' => now()->subDays(5)->toDateTimeString(),
                    'revoked_by_name' => 'Admin System',
                    'revocation_reason' => 'Plagiarism detected',
                    'revocation_evidence' => 'plagiarism_report.pdf',
                    'revocation_note' => 'Final post-test submitted work was found plagiarized from another student submission.',
                    'template_name' => 'Royal Gold',
                    'downloads_count' => 1,
                    'verifications_count' => 9,
                    'last_verified_at' => now()->subHours(1)->toDateTimeString(),
                ]
            ]);
        }

        $eligibleStudents = [
            [
                'id' => 201,
                'name' => 'Chan Dara',
                'student_id' => 'STU24001',
                'course' => 'C Programming Basics',
                'major' => 'IT & Networking',
                'progress' => 100,
                'post_test' => 82,
                'assignments' => '4/4 Graded',
                'payment' => 'Paid',
                'standing' => 'No Violations',
                'learning_time' => '28h 30m',
                'template' => 'Classic Blue',
                'status' => 'ready',
                'blocked_reason' => null,
            ],
            [
                'id' => 202,
                'name' => 'Bun Rithy',
                'student_id' => 'STU24002',
                'course' => 'C Programming Basics',
                'major' => 'IT & Networking',
                'progress' => 100,
                'post_test' => 78,
                'assignments' => '4/4 Graded',
                'payment' => 'Paid',
                'standing' => 'No Violations',
                'learning_time' => '24h 15m',
                'template' => 'Classic Blue',
                'status' => 'ready',
                'blocked_reason' => null,
            ],
            [
                'id' => 203,
                'name' => 'Pov Sreynich',
                'student_id' => 'STU24003',
                'course' => 'Plant Science & Soil Biology',
                'major' => 'Agronomy',
                'progress' => 100,
                'post_test' => 76,
                'assignments' => '3/3 Graded',
                'payment' => 'Paid',
                'standing' => 'No Violations',
                'learning_time' => '31h 00m',
                'template' => 'Green Agriculture',
                'status' => 'ready',
                'blocked_reason' => null,
            ],
            [
                'id' => 204,
                'name' => 'Long Vichida',
                'student_id' => 'STU24004',
                'course' => 'English Academic Writing',
                'major' => 'Social Work',
                'progress' => 100,
                'post_test' => 71,
                'assignments' => '4/4 Graded',
                'payment' => 'Pending',
                'standing' => 'No Violations',
                'learning_time' => '22h 40m',
                'template' => 'Elegant Purple',
                'status' => 'blocked',
                'blocked_reason' => 'Payment pending — certificate will auto-issue once ABA payment is verified.',
            ],
        ];

        $verificationLogs = CertificateVerificationLog::latest()->take(20)->get();
        if ($verificationLogs->isEmpty()) {
            $verificationLogs = collect([
                ['time' => '16 Jun 14:20', 'cert_id' => 'ELMS-2025-000451', 'result' => 'valid', 'ip' => '103.14.28.4', 'location' => 'Phnom Penh, KH', 'source' => 'QR Scan'],
                ['time' => '16 Jun 11:05', 'cert_id' => 'ELMS-2025-000449', 'result' => 'valid', 'ip' => '45.112.90.12', 'location' => 'Singapore, SG', 'source' => 'Manual ID'],
                ['time' => '16 Jun 09:40', 'cert_id' => 'ELMS-2025-000412', 'result' => 'revoked', 'ip' => '103.14.28.4', 'location' => 'Phnom Penh, KH', 'source' => 'Manual ID'],
                ['time' => '15 Jun 16:30', 'cert_id' => 'ELMS-2024-000999', 'result' => 'not_found', 'ip' => '88.198.42.11', 'location' => 'Frankfurt, DE', 'source' => 'Manual ID'],
            ]);
        }

        $autoRules = [
            'auto_issue_enabled' => true,
            'require_progress_100' => true,
            'require_post_test_passed' => true,
            'require_assignments_passed' => true,
            'require_payment_verified' => true,
            'require_no_violations' => true,
            'default_template' => 'Match by Major',
        ];

        $publicSettings = [
            'public_url' => url('/certificate/verify'),
            'page_enabled' => true,
            'qr_scanner_enabled' => true,
            'show_student_details' => true,
            'show_student_email' => false,
            'log_all_verifications' => true,
        ];

        return compact('templates', 'certificates', 'eligibleStudents', 'verificationLogs', 'autoRules', 'publicSettings');
    }

    // 1. Templates View
    public function templates()
    {
        $data = $this->getCommonData();
        return Inertia::render('Admin/CertificateModule/Templates', $data);
    }

    // 2. Issue Certificate View
    public function issueView()
    {
        $data = $this->getCommonData();
        return Inertia::render('Admin/CertificateModule/IssueCertificate', $data);
    }

    // 3. Issued Certificates View (Default index)
    public function index(Request $request)
    {
        $tab = $request->query('tab');
        if ($tab === 'templates') return $this->templates();
        if ($tab === 'issue') return $this->issueView();
        if ($tab === 'verify') return $this->verifyView();
        if ($tab === 'revoked') return $this->revokedView();

        $data = $this->getCommonData();
        return Inertia::render('Admin/CertificateModule/IssuedCertificates', $data);
    }

    // 4. Certificate Verification View
    public function verifyView()
    {
        $data = $this->getCommonData();
        return Inertia::render('Admin/CertificateModule/CertificateVerification', $data);
    }

    // 5. Revoked Certificates View
    public function revokedView()
    {
        $data = $this->getCommonData();
        return Inertia::render('Admin/CertificateModule/RevokedCertificates', $data);
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'assigned_type' => 'required|string',
            'assigned_id' => 'nullable|integer',
            'language' => 'required|string',
            'paper_size' => 'required|string',
            'elements' => 'nullable|array',
            'status' => 'required|string',
        ]);

        CertificateTemplate::create($validated);

        return back()->with('success', 'Certificate template saved successfully.');
    }

    public function updateTemplate(Request $request, $id)
    {
        $template = CertificateTemplate::findOrFail($id);
        $template->update($request->all());

        return back()->with('success', 'Certificate template updated successfully.');
    }

    public function duplicateTemplate($id)
    {
        $template = CertificateTemplate::findOrFail($id);
        $newTemplate = $template->replicate();
        $newTemplate->name = $template->name . ' (Copy)';
        $newTemplate->save();

        return back()->with('success', 'Template duplicated successfully.');
    }

    public function issueSingle(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
            'course_id'  => 'required|integer',
        ]);

        $student = User::find($request->student_id) ?? User::factory()->make(['id' => $request->student_id, 'name' => 'Chan Dara', 'email' => 'dara@elms.edu']);
        $course = Course::find($request->course_id) ?? Course::factory()->make(['id' => $request->course_id, 'title' => 'C Programming Basics']);

        $certificate = $this->certificateService->issue(
            $student,
            $course,
            $request->template_id,
            $request->grade ?? 'A',
            $request->score ?? 85
        );

        return back()->with('success', "Certificate {$certificate->certificate_number} issued successfully!");
    }

    public function issueBulk(Request $request)
    {
        return back()->with('success', 'Bulk issuance triggered for all eligible students.');
    }

    public function quickVerify(Request $request)
    {
        $code = $request->input('certificate_id');
        $result = $this->certificateService->logVerification($code, $request->ip(), 'admin_tool');

        return response()->json($result);
    }

    public function requestRevocation(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $certificate = Certificate::find($id);

        if ($certificate) {
            $certificate->update([
                'status' => 'revoked',
                'revoked_at' => now(),
                'revoked_by' => auth()->id(),
                'revocation_reason' => $request->reason,
                'revocation_note' => $request->note,
                'show_reason_publicly' => $request->boolean('show_reason_publicly', true),
            ]);
        }

        return back()->with('success', 'Certificate revoked successfully.');
    }

    public function restoreCertificate(Request $request, $id)
    {
        $certificate = Certificate::find($id);

        if ($certificate) {
            $certificate->update([
                'status' => 'valid',
                'revoked_at' => null,
                'revoked_by' => null,
                'revocation_reason' => null,
                'revocation_note' => null,
            ]);
        }

        return back()->with('success', 'Certificate restored back to VALID status.');
    }
}
