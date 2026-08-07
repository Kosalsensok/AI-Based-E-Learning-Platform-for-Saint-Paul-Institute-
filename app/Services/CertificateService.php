<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateVerificationLog;
use App\Models\Course;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateService
{
    /**
     * Generate standard sequential Certificate Number: ELMS-YYYY-XXXXXX
     */
    public function generateCertificateNumber(): string
    {
        $year = date('Y');
        $lastCert = Certificate::latest('id')->first();
        $nextSeq = ($lastCert ? $lastCert->id : 0) + 1;
        $seqString = str_pad((string)$nextSeq, 6, '0', STR_PAD_LEFT);

        return "ELMS-{$year}-{$seqString}";
    }

    /**
     * Issue a new certificate or return existing valid certificate
     */
    public function issue(User $student, Course $course, ?int $templateId = null, string $grade = 'A', int $score = 85): Certificate
    {
        $existing = Certificate::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            if ($existing->status === 'revoked') {
                $existing->update([
                    'status' => 'valid',
                    'revoked_at' => null,
                    'revoked_by' => null,
                    'revocation_reason' => null,
                    'revocation_note' => null,
                ]);
            }
            return $existing;
        }

        $certNumber = $this->generateCertificateNumber();
        $verifyCode = strtoupper(Str::random(10));

        $certificate = Certificate::create([
            'student_id'         => $student->id,
            'course_id'          => $course->id,
            'major_id'           => $student->major_id ?? $course->department?->faculty_id,
            'template_id'        => $templateId,
            'certificate_number' => $certNumber,
            'verification_code'  => $verifyCode,
            'grade'              => $grade,
            'score'              => $score,
            'status'             => 'valid',
            'issued_at'          => now(),
            'audit_trail'        => [
                [
                    'action'     => 'issued',
                    'by'         => auth()->user()?->name ?? 'System',
                    'timestamp'  => now()->toIso8601String(),
                    'details'    => 'Certificate generated and registered.',
                ]
            ]
        ]);

        return $certificate;
    }

    /**
     * Verify certificate and log verification attempt
     */
    public function logVerification(string $codeOrId, ?string $ipAddress = '127.0.0.1', string $source = 'manual_id'): array
    {
        $cert = Certificate::with(['student', 'course', 'major', 'template'])
            ->where('verification_code', $codeOrId)
            ->orWhere('certificate_number', $codeOrId)
            ->first();

        if (!$cert) {
            CertificateVerificationLog::create([
                'certificate_number' => $codeOrId,
                'result'             => 'not_found',
                'ip_address'         => $ipAddress,
                'location'           => 'Phnom Penh, KH',
                'source'             => $source,
            ]);

            return [
                'status'  => 'not_found',
                'message' => 'Certificate ID not found in official registry.',
            ];
        }

        $result = $cert->status; // valid or revoked

        $cert->increment('verifications_count');
        $cert->update(['last_verified_at' => now()]);

        CertificateVerificationLog::create([
            'certificate_id'     => $cert->id,
            'certificate_number' => $cert->certificate_number,
            'result'             => $result,
            'ip_address'         => $ipAddress,
            'location'           => 'Phnom Penh, KH',
            'source'             => $source,
        ]);

        return [
            'status'               => $result,
            'certificate'          => $cert,
            'student_name'         => $cert->student?->name ?? 'N/A',
            'student_id'           => 'STU' . str_pad($cert->student_id ?? 1, 5, '0', STR_PAD_LEFT),
            'course_name'          => $cert->course?->title ?? 'N/A',
            'major_name'           => $cert->major?->name ?? 'IT & Networking',
            'grade'                => $cert->grade ?? 'A',
            'score'                => $cert->score ?? 85,
            'issued_at'            => $cert->issued_at ? $cert->issued_at->format('d F Y') : 'N/A',
            'certificate_number'   => $cert->certificate_number,
            'revoked_at'           => $cert->revoked_at ? $cert->revoked_at->format('d F Y') : null,
            'revocation_reason'    => $cert->show_reason_publicly ? $cert->revocation_reason : 'Revoked by institution policy',
        ];
    }
}
