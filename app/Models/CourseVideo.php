<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseVideo extends Model
{
    protected $fillable = [
        'course_id',
        'lesson_id',
        'title',
        'cloudinary_url',
        'video_path',
        'duration',
        'duration_seconds',
        'file_size',
        'status',
        'transcode_progress',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'transcode_progress' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
