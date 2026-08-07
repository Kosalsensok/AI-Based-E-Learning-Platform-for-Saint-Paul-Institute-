<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'type',
        'rich_media_url',
        'btn1_text',
        'btn1_deeplink',
        'btn2_text',
        'target_audience',
        'timing_type',
        'scheduled_at',
        'status',
        'sent_count',
        'open_rate',
        'click_rate',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];
}
