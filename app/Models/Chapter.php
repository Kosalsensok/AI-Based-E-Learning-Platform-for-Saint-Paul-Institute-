<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $fillable = [
        'module_id', 'chapter_number', 'title', 'kh_title', 'order'
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }
}
