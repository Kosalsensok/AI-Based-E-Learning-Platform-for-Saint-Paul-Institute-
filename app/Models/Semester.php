<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = [
        'academic_year_id',
        'code',
        'name',
        'parent_year',
        'semester_num',
        'start_date',
        'end_date',
        'enrollment_open',
        'enrollment_close',
        'midterm_exam',
        'final_exam',
        'payment_due',
        'late_fee',
        'status',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
