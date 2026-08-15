<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiGeneratedContent extends Model
{
    protected $table = 'ai_generated_content';

    protected $fillable = [
        'course_id',
        'lesson_id',
        'type',
        'title',
        'content_json',
        'status',
        'approved_at',
    ];

    protected $casts = [
        'content_json' => 'array',
        'approved_at' => 'datetime',
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
