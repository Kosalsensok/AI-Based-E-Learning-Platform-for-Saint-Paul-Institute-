<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id',
        'author_name',
        'author_role',
        'content',
        'likes_count',
        'is_best_answer',
        'is_teacher_answer',
        'toxic_score',
    ];

    protected $casts = [
        'is_best_answer' => 'boolean',
        'is_teacher_answer' => 'boolean',
    ];
}
