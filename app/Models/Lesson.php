<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'module_id', 'chapter_id', 'course_id', 'title', 'kh_title', 'type', 'file_path', 'file_url',
        'video_url', 'content', 'ai_summary', 'thumbnail', 'duration_seconds', 'order', 'is_free_preview', 'downloadable'
    ];

    protected $casts = [
        'is_free_preview' => 'boolean',
        'downloadable' => 'boolean',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function videos()
    {
        return $this->hasMany(CourseVideo::class);
    }

    public function materials()
    {
        return $this->hasMany(CourseMaterial::class);
    }

    public function aiContents()
    {
        return $this->hasMany(AiGeneratedContent::class);
    }

    public function labIntegrations()
    {
        return $this->hasMany(LabIntegration::class);
    }
}
