<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabIntegration extends Model
{
    protected $fillable = [
        'course_id',
        'lesson_id',
        'title',
        'lab_type',
        'config_json',
        'provider_url',
        'status',
    ];

    protected $casts = [
        'config_json' => 'array',
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
