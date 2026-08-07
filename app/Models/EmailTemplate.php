<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'trigger_event',
        'subject_kh',
        'subject_en',
        'html_body',
        'from_name',
        'reply_to',
        'attach_pdf_receipt',
        'send_delay',
        'status',
        'sent_count',
        'open_rate',
    ];

    protected $casts = [
        'attach_pdf_receipt' => 'boolean',
    ];
}
