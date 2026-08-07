<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'name',
        'assigned_type',
        'assigned_id',
        'language',
        'paper_size',
        'background_image',
        'status',
        'elements',
    ];

    protected $casts = [
        'elements' => 'array',
    ];
}
