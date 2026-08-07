<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportedContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_code',
        'reported_by',
        'reason',
        'content_type',
        'author_name',
        'comment_text',
        'toxic_score',
        'evidence_url',
        'status',
        'decision',
        'admin_note',
    ];
}
