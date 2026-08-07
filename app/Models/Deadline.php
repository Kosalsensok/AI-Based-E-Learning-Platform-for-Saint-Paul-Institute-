<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deadline extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'course_id',
        'linked_type',
        'linked_id',
        'title',
        'deadline_type',
        'due_at',
        'grace_days',
        'penalty_percent',
        'applicable_to',
        'show_countdown',
        'auto_reminder_24h',
        'auto_reminder_1h',
        'auto_lock',
        'message_kh',
        'message_en',
        'status',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'show_countdown' => 'boolean',
        'auto_reminder_24h' => 'boolean',
        'auto_reminder_1h' => 'boolean',
        'auto_lock' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
