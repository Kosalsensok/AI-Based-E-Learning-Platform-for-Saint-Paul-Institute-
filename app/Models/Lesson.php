<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'module_id', 'course_id', 'title', 'type', 'file_path',
        'video_url', 'content', 'thumbnail', 'duration_seconds', 'order', 'is_free_preview', 'downloadable'
    ];

    protected $casts = [
        'is_free_preview' => 'boolean',
        'downloadable' => 'boolean',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
