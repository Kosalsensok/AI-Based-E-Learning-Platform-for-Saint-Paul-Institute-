<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'major_id',
        'template_id',
        'certificate_number',
        'verification_code',
        'grade',
        'score',
        'status',
        'revoked_at',
        'revoked_by',
        'revocation_reason',
        'revocation_evidence',
        'revocation_note',
        'show_reason_publicly',
        'downloads_count',
        'verifications_count',
        'last_verified_at',
        'file_path',
        'audit_trail',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'revoked_at' => 'datetime',
        'last_verified_at' => 'datetime',
        'show_reason_publicly' => 'boolean',
        'audit_trail' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }

    public function revokedByAdmin()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function scopeValid($query)
    {
        return $query->where('status', 'valid');
    }

    public function scopeRevoked($query)
    {
        return $query->where('status', 'revoked');
    }
}
