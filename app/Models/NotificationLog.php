<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'notif_code',
        'channel',
        'type',
        'title',
        'recipient_name',
        'recipient_email',
        'recipient_device',
        'status',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'failure_reason',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];
}
