<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'course_id',
        'module_id',
        'major_id',
        'type',
        'title',
        'description',
        'learning_mode',
        'start_at',
        'end_at',
        'timezone',
        'repeat_rule',
        'location_type',
        'room_number',
        'meeting_link',
        'capacity',
        'notify_email',
        'notify_push',
        'notify_announcement',
        'reminder_15m',
        'reminder_1h',
        'auto_record',
        'status',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'notify_email' => 'boolean',
        'notify_push' => 'boolean',
        'notify_announcement' => 'boolean',
        'reminder_15m' => 'boolean',
        'reminder_1h' => 'boolean',
        'auto_record' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id');
    }
}
