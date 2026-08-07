<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_code',
        'question_text',
        'course_name',
        'chapter_name',
        'student_name',
        'student_year',
        'views_count',
        'status',
        'priority',
        'sla_overdue',
        'ai_draft_answer',
        'ai_confidence',
        'final_answer',
        'answered_by',
        'helpful_votes',
        'is_faq',
    ];

    protected $casts = [
        'sla_overdue' => 'boolean',
        'is_faq' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id')->withDefault([
            'title' => $this->course_name ?? 'C Programming Basics'
        ]);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id')->withDefault([
            'name' => $this->student_name ?? 'Student'
        ]);
    }
}
