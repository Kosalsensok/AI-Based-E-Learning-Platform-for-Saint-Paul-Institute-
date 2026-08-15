<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    protected $fillable = [
        'course_id',
        'lesson_id',
        'title',
        'type',
        'file_url',
        'file_path',
        'file_size',
        'file_name',
        'slide_count',
        'download_count',
        'self_paced_only',
        'status',
    ];

    protected $casts = [
        'slide_count' => 'integer',
        'download_count' => 'integer',
        'self_paced_only' => 'boolean',
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
