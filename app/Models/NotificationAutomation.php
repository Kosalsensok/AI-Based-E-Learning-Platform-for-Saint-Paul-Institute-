<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationAutomation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'trigger_event',
        'trigger_conditions',
        'audience_filter',
        'action_channels',
        'push_message',
        'email_template_id',
        'timing',
        'frequency',
        'max_sends',
        'status',
    ];

    protected $casts = [
        'trigger_conditions' => 'array',
        'audience_filter' => 'array',
        'action_channels' => 'array',
    ];
}
