<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'course_name',
        'module_name',
        'author_name',
        'author_role',
        'replies_count',
        'likes_count',
        'views_count',
        'status',
        'is_pinned',
        'is_locked',
        'is_solved',
        'is_hot',
        'toxic_score',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_solved' => 'boolean',
        'is_hot' => 'boolean',
    ];

    public function comments()
    {
        return $table = $this->hasMany(DiscussionComment::class, 'thread_id');
    }
}
