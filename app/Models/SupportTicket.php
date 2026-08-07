<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'student_name',
        'student_email',
        'subject',
        'category',
        'priority',
        'assigned_to',
        'sla_time_left',
        'is_overdue',
        'status',
        'message',
        'evidence_url',
        'timeline',
    ];

    protected $casts = [
        'is_overdue' => 'boolean',
        'timeline' => 'array',
    ];
}
