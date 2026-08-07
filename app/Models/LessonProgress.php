<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    protected $table = 'lesson_progress';

    protected $fillable = [
        'user_id', 'lesson_id', 'percent', 'seconds_watched', 'completed_at', 'client_updated_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'client_updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
