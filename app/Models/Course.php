<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'major_id', 'teacher_id', 'title', 'code', 'description',
        'learning_mode', 'is_paid', 'price', 'status', 'thumbnail',
        'submitted_at', 'reviewed_at', 'rejection_note'
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'price' => 'decimal:2',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
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
