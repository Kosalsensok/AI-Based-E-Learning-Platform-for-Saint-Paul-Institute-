<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateVerificationLog extends Model
{
    protected $fillable = [
        'certificate_id',
        'certificate_number',
        'result',
        'ip_address',
        'location',
        'source',
    ];

    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }
}
