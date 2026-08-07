<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_kh',
        'title_en',
        'body_kh',
        'body_en',
        'audience_type',
        'audience_filters',
        'priority',
        'delivery_channels',
        'scheduled_at',
        'is_pinned',
        'pin_days',
        'require_ack',
        'allow_comments',
        'status',
        'sent_count',
        'read_rate',
        'attachments',
    ];

    protected $casts = [
        'audience_filters' => 'array',
        'delivery_channels' => 'array',
        'attachments' => 'array',
        'is_pinned' => 'boolean',
        'require_ack' => 'boolean',
        'allow_comments' => 'boolean',
        'scheduled_at' => 'datetime',
    ];
}
